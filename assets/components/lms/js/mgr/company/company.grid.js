LMS.grid.Companies = function(config) {
	config = config || {};

	Ext.applyIf(config, {
		url: LMS.config.connector_url,
		baseParams: {
			action: 'mgr/company/getlist',
		},
		fields: this.getFields(),
		columns: this.getColumns(config),
		tbar: this.getTopBar(config),
		sm: new Ext.grid.CheckboxSelectionModel(),
		autoHeight: true,
		paging: true,
		remoteSort: true,
		viewConfig: {
			forceFit: true,
			enableRowBody: true,
			showPreview: true,
			getRowClass: function (rec, ri, p) {
				var cls = [];
				if (rec.data.deleted) {
					cls.push('lms-row-deleted');
				}
				return cls.join(' ');
			}
		},
		cls: MODx.modx23 ? 'modx23' : 'modx22',
		stateful: true,
		stateId: 'lms-company-state',
	});
	LMS.grid.Companies.superclass.constructor.call(this,config);
	this.getStore().sortInfo = {
		field: 'id',
		direction: 'DESC'
	};
};
Ext.extend(LMS.grid.Companies, MODx.grid.Grid, {

	getFields: function(config) {
		return [
			'id', 'name', 'description', 'actions'
		];
	},

	getColumns: function(config) {
		return [{
				header: _('id'),
				dataIndex: 'id',
				width: 35,
				sortable: true,
			},{
				header: _('company_name'),
				dataIndex: 'name',
				width: 75,
				sortable: true,
				renderer: function(value, metaData, record) {
					return LMS.utils.companyLink(value, record['data']['id'])
				},
			},{
				header: _('company_description'),
				dataIndex: 'description',
				width: 75,
				sortable: false,
			},{
				header: _('company_actions'),
				dataIndex: 'actions',
				renderer: LMS.utils.renderActions,
				sortable: false,
				width: 75,
				id: 'actions'
			}
		];
	},

	getTopBar: function(config) {
		var tbar = [];
		tbar.push({
			text: (MODx.modx23
				? '<i class="icon icon-plus"></i> '
				: '<i class="fa fa-plus"></i> ')
			+ _('company_create'),
			handler: this.createCompany,
			scope: this,
		});
		tbar.push({
			text: MODx.modx23
				? '<i class="icon icon-trash-o action-red"></i>'
				: '<i class="fa fa-trash-o action-red"></i>',
			handler: this.deleteCompany,
			scope: this,
		});
		tbar.push('->');
		tbar.push({
			xtype: 'lms-field-search',
			width: 250,
			listeners: {
				search: {fn: function(field) {
					this._doSearch(field);
				}, scope: this},
				clear: {fn: function(field) {
					field.setValue('');
					this._clearSearch();
				}, scope: this},
			}
		});

		return tbar;
	},

	getMenu: function (grid, rowIndex) {
		var ids = this._getSelectedIds();

		var row = grid.getStore().getAt(rowIndex);
		var menu = LMS.utils.getMenu(row.data['actions'], this, ids);

		this.addContextMenuItem(menu);
	},

	onClick: function (e) {
		var elem = e.getTarget();
		if (elem.nodeName == 'BUTTON') {
			var row = this.getSelectionModel().getSelected();
			if (typeof(row) != 'undefined') {
				var action = elem.getAttribute('action');
				if (action == 'showMenu') {
					var ri = this.getStore().find('id', row.id);
					return this._showMenu(this, ri, e);
				}
				else if (typeof this[action] === 'function') {
					this.menu.record = row.data;
					return this[action](this, e);
				}
			}
		}
		return this.processEvent('click', e);
	},

	createCompany: function(btn, e) {
		var w = MODx.load({
			xtype: 'modextra-company-create',
			id: Ext.id(),
			listeners: {
				success: {
					fn: function () {
						this.refresh();
					}, scope: this
				}
			}
		});
		w.reset();
		w.setValues({active: true});
		w.show(e.target);
	},

	editCompany: function(btn, e, row) {
		if (typeof(row) != 'undefined') {
			this.menu.record = row.data;
		}
		else if (!this.menu.record) {
			return false;
		}
		var id = this.menu.record.id;

		MODx.Ajax.request({
			url: this.config.url,
			params: {
				action: 'mgr/company/get',
				id: id
			},
			listeners: {
				success: {
					fn: function (r) {
						var w = MODx.load({
							xtype: 'modextra-company-update',
							id: Ext.id(),
							record: r,
							listeners: {
								success: {
									fn: function () {
										this.refresh();
									}, scope: this
								}
							}
						});
						w.reset();
						w.setValues(r.object);
						w.show(e.target);
					}, scope: this
				}
			}
		});
	},

	companyAction: function(method) {
		var ids = this._getSelectedIds();
		if (!ids.length) {
			return false;
		}
		MODx.Ajax.request({
			url: LMS.config.connector_url,
			params: {
				action: 'mgr/company/multiple',
				method: method,
				ids: Ext.util.JSON.encode(ids),
			},
			listeners: {
				success: {
					fn: function () {
						this.refresh();
					}, scope: this
				},
				failure: {
					fn: function (response) {
						MODx.msg.alert(_('error'), response.message);
					}, scope: this
				},
			}
		})
	},

	deleteCompany: function(btn,e) {
		this.companyAction('delete');
	},

	_getSelectedIds: function() {
		var ids = [];
		var selected = this.getSelectionModel().getSelections();

		for (var i in selected) {
			if (!selected.hasOwnProperty(i)) {
				continue;
			}
			ids.push(selected[i]['id']);
		}

		return ids;
	},

	_doSearch: function (tf) {
		this.getStore().baseParams.query = tf.getValue();
		this.getBottomToolbar().changePage(1);
	},

	_clearSearch: function() {
		this.getStore().baseParams.query = '';
		this.getBottomToolbar().changePage(1);
	},

});
Ext.reg('lms-grid-company', LMS.grid.Companies);
LMS.grid.Statistic = function(config) {
	config = config || {};

	Ext.applyIf(config, {
		url: LMS.config.connector_url,
		baseParams: {
			action: 'mgr/statistic/getlist',
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
		stateId: 'lms-statistic-state',
	});
	LMS.grid.Statistic.superclass.constructor.call(this,config);
	this.getStore().sortInfo = {
		field: 'editedon',
		direction: 'DESC'
	};
};
Ext.extend(LMS.grid.Statistic, MODx.grid.Grid, {

	getFields: function(config) {
		return [
			'id', 'user_id', 'parent', 'item',
			'editedon', 'author', 'finished', 'progress', 'actions'
		];
	},

	getColumns: function(config) {
		return [{
				header: _('id'),
				dataIndex: 'id',
				width: 35,
				sortable: true,
			},{
				header: _('user'),
				dataIndex: 'author',
				width: 75,
				sortable: true,
				renderer: function(value, metaData, record) {
					return LMS.utils.userLink(value, record['data']['user_id'])
				},
			},{
				header: _('statistic_object'),
				dataIndex: 'item',
				width: 75,
				sortable: true,
				renderer: function(value, metaData, record) {
					return LMS.utils.ticketLink(value, record['data']['parent'], true)
				},
			},{
				header: _('statistic_date'),
				dataIndex: 'editedon',
				width: 75,
				sortable: true,
				renderer: LMS.utils.formatDate
			},{
				header: _('statistic_progress'),
				dataIndex: 'progress',
				width: 35,
				sortable: true,
				renderer: function(value) {
					return value + '%'
				}
			},{
				header: _('statistic_finished'),
				dataIndex: 'finished',
				width: 35,
				sortable: true,
				renderer: LMS.utils.renderBoolean
			}, {
				header: _('statistic_actions'),
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

	editEntry: function() {
		
	},

	entryAction: function(method) {
		var ids = this._getSelectedIds();
		if (!ids.length) {
			return false;
		}
		MODx.Ajax.request({
			url: LMS.config.connector_url,
			params: {
				action: 'mgr/statistic/multiple',
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

	deleteEntry: function(btn,e) {
		this.entryAction('delete');
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
Ext.reg('lms-grid-statistic', LMS.grid.Statistic);
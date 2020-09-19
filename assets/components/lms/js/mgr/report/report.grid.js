LMS.grid.Report = function(config) {
	config = config || {};

	Ext.applyIf(config, {
		url: LMS.config.connector_url,
		baseParams: {
			action: 'mgr/report/getlist',
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
		stateId: 'lms-report-state',
	});
	LMS.grid.Report.superclass.constructor.call(this,config);
	this.getStore().sortInfo = {
		field: 'name',
		direction: 'ASC'
	};
};
Ext.extend(LMS.grid.Report, MODx.grid.Grid, {

	getFields: function(config) {
		return [
			'id', 'course_id', 'company_id', 'pagetitle', 'name',
			'students', 'deleted', 'roles'
		];
	},

	getColumns: function(config) {
		return [{
				header: _('pagetitle'),
				dataIndex: 'pagetitle',
				width: config.standalone ? 100 : 150,
				sortable: true,
				renderer: function(value, metaData, record) {
					return LMS.utils.ticketLink(value, record['data']['course_id'])
				},
				id: 'pagetitle'
			}, {
				header: _('company'),
				dataIndex: 'name',
				width: 75,
				sortable: true,
				renderer: function(value, metaData, record) {
					return LMS.utils.companyLink(value, record['data']['company_id'])
				},
			}, {
				header: _('report_roles'),
				dataIndex: 'roles',
				width: 35,
				sortable: false,
			}, {
				header: _('report_students_quantity'),
				dataIndex: 'students',
				width: 35,
				sortable: true,
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
Ext.reg('lms-grid-report', LMS.grid.Report);
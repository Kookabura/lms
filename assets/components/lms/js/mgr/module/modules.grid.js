LMS.grid.Modules = function(config) {
	config = config || {};

	Ext.applyIf(config, {
		url: LMS.config.connector_url,
		baseParams: {
			action: 'mgr/module/getlist',
			parent: config.parent || 0,
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
			autoFill: true,
			showPreview: true,
			scrollOffset: 0,
			getRowClass: function (rec, ri, p) {
				var cls = [];
				if (!rec.data.published) {
					cls.push('lms-row-unpublished');
				}
				if (rec.data.deleted) {
					cls.push('lms-row-deleted');
				}
				return cls.join(' ');
			}
		},
		cls: MODx.modx23 ? 'modx23' : 'modx22',
		standalone: false,
		stateful: true,
		stateId: 'lms-modules-state',
	});
	LMS.grid.Modules.superclass.constructor.call(this,config);
	this.getStore().sortInfo = {
		field: 'createdon',
		direction: 'DESC'
	};
};
Ext.extend(LMS.grid.Modules, MODx.grid.Grid, {

	getFields: function(config) {
		return [
			'id', 'pagetitle', 'published', 'deleted',
			'publishedon', 'createdon',
			'createdby', 'class_key', 'section_id', 'course',
			'preview_url', 'actions'
		];
	},

	getColumns: function(config) {
		return [{
				header: _('id'),
				dataIndex: 'id',
				width: 35,
				sortable: true,
			},{
				header: _('module_pagetitle'),
				dataIndex: 'pagetitle',
				width: config.standalone ? 100 : 150,
				sortable: true,
				renderer: function(value, metaData, record) {
					return LMS.utils.ticketLink(value, record['data']['id'])
				},
				id: 'pagetitle'
			},{
				header: _('module_createdon'),
				dataIndex: 'createdon',
				width: 50,
				sortable: true,
				renderer: LMS.utils.formatDate,
			},{
				header: _('module_publishedon'),
				dataIndex: 'publishedon',
				width: 50,
				sortable: true,
				renderer: LMS.utils.formatDate,
			},{
				header: _('module_parent'),
				dataIndex: 'course',
				width: 75,
				sortable: true,
				renderer: function(value, metaData, record) {
					return LMS.utils.ticketLink(value, record['data']['section_id'], true)
				},
				hidden: !config.standalone,
			},{
				header: _('module_actions'),
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
		if (!config.standalone) {
			tbar.push({
				text: (MODx.modx23
					? '<i class="icon icon-plus"></i> '
					: '<i class="fa fa-plus"></i> ')
				+ _('module_create'),
				handler: this.createModules,
				scope: this,
			});
		}
		tbar.push({
			text: MODx.modx23
				? '<i class="icon icon-trash-o action-red"></i>'
				: '<i class="fa fa-trash-o action-red"></i>',
			handler: this._emptyRecycleBin,
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
		else if (elem.nodeName == 'A' && elem.href.match(/(\?|\&)a=resource/)) {
			if (e.button == 1 || (e.button == 0 && e.ctrlKey == true)) {
				// Bypass
			}
			else if (elem.target && elem.target == '_blank') {
				// Bypass
			}
			else {
				e.preventDefault();
				MODx.loadPage('', elem.href);
			}
		}
		return this.processEvent('click', e);
	},

	createModule: function() {
		var createPage = MODx.action
			? MODx.action['resource/create']
			: 'resource/create';
		location.href = 'index.php?a=' + createPage + '&class_key=Modules&parent=' + MODx.request.id + '&context_key=' + MODx.ctx;
	},

	editModule: function() {
		var action = MODx.action
			? MODx.action['resource/update']
			: 'resource/update';
		MODx.loadPage(action, 'id=' + this.menu.record.id);
	},

	viewModule: function() {
		window.open(this.menu.record['preview_url']);
		return false;
	},

	ticketAction: function(method) {
		var ids = this._getSelectedIds();
		if (!ids.length) {
			return false;
		}
		MODx.Ajax.request({
			url: LMS.config.connector_url,
			params: {
				action: 'mgr/module/multiple',
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

	deleteModule: function(btn,e) {
		this.ticketAction('delete');
	},

	undeleteModule: function(btn,e) {
		this.ticketAction('undelete');
	},

	publishModule: function(btn,e) {
		this.ticketAction('publish');
	},

	unpublishModule: function(btn,e) {
		this.ticketAction('unpublish');
	},

	duplicateModule: function(btn,e) {
		var r = this.menu.record;
		var w = MODx.load({
			xtype: 'modx-window-resource-duplicate',
			resource: r.id,
			hasChildren: 0,
			listeners: {
				success: {fn:function() {
					this.refresh();
				},scope:this}
			}
		});
		w.config.hasChildren = 0;
		w.setValues(r.data);
		w.show();
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

	_emptyRecycleBin: function() {
		MODx.msg.confirm({
			title: _('empty_recycle_bin'),
			text: _('empty_recycle_bin_confirm'),
			url: MODx.modx23
				? MODx.config.connector_url
				: MODx.config.connectors_url + 'resource/index.php',
			params: {
				action: MODx.modx23
					? 'resource/emptyRecycleBin'
					: 'emptyRecycleBin',
			},
			listeners: {
				success: {fn:function() {
					this.refresh();
				},scope:this}
			}
		});
	},

});
Ext.reg('lms-grid-modules', LMS.grid.Modules);
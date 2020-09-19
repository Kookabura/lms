LMS.combo.User = function(config) {
	config = config || {};
	Ext.applyIf(config, {
		name: 'user',
		fieldLabel: config.name || 'createdby',
		hiddenName: config.name || 'createdby',
		displayField: 'username',
		valueField: 'id',
		anchor: '99%',
		fields: ['username', 'id', 'fullname'],
		pageSize: 20,
		url: MODx.modx23
			? MODx.config.connector_url
			: MODx.config.connectors_url + 'security/user.php',
		typeAhead: false,
		editable: true,
		allowBlank: false,
		baseParams: {
			action: MODx.modx23
				? 'security/user/getlist'
				: 'getlist',
			combo: 1,
			id: config.value
		},
		tpl: new Ext.XTemplate('\
			<tpl for=".">\
				<div class="x-combo-list-item lms-list-item">\
					<span>\
						<small>({id})</small>\
						<b>{username}</b>\
						<tpl if="fullname"> - {fullname}</tpl>\
					</span>\
				</div>\
			</tpl>',
			{compiled: true}
		),
	});
	LMS.combo.User.superclass.constructor.call(this, config);
};
Ext.extend(LMS.combo.User, MODx.combo.ComboBox);
Ext.reg('lms-combo-user', LMS.combo.User);


MODx.combo.Course = function(config) {
	config = config || {};
	Ext.applyIf(config, {
		fieldLabel: _('resource_parent'),
		description: '<b>[[*parent]]</b><br />' + _('resource_parent_help'),
		fields: ['id', 'pagetitle', 'parents'],
		valueField: 'id',
		displayField: 'pagetitle',
		name: 'parent-cmb',
		hiddenName: 'parent-cmp',
		url: LMS.config.connector_url,
		baseParams: {
			action: 'mgr/course/getlist',
			combo: 1,
			id: config.value
		},
		pageSize: 10,
		width: 300,
		typeAhead: false,
		editable: true,
		allowBlank: false,
		tpl: new Ext.XTemplate('\
			<tpl for=".">\
				<div class="x-combo-list-item lms-list-item">\
					<tpl if="parents">\
						<span class="parents">\
							<tpl for="parents">\
								<nobr>{pagetitle} / </nobr>\
							</tpl>\
						</span>\
					</tpl>\
					<span>\
						<small>({id})</small>\
						<b>{pagetitle}</b>\
					</span>\
				</div>\
			</tpl>',
			{compiled: true}
		),
	});
	MODx.combo.Course.superclass.constructor.call(this, config);
};
Ext.extend(MODx.combo.Course, MODx.combo.ComboBox);
Ext.reg('lms-combo-course', MODx.combo.Course);


LMS.combo.Template = function(config) {
	config = config || {};
	Ext.applyIf(config, {
		name: 'properties[lms][template]',
		hiddenName: 'properties[lms][template]',
		url: MODx.modx23
			? MODx.config.connector_url
			: MODx.config.connectors_url + 'element/template.php',
		baseParams: {
			action: MODx.modx23
				? 'element/template/getlist'
				: 'getlist',
			combo: 1,
		}
	});
	LMS.combo.Template.superclass.constructor.call(this, config);
};
Ext.extend(LMS.combo.Template, MODx.combo.Template);
Ext.reg('lms-children-combo-template', LMS.combo.Template);


LMS.combo.Search = function(config) {
	config = config || {};
	Ext.applyIf(config, {
		xtype: 'twintrigger',
		ctCls: 'x-field-search',
		allowBlank: true,
		msgTarget: 'under',
		emptyText: _('search'),
		name: 'query',
		triggerAction: 'all',
		clearBtnCls: 'x-field-search-clear',
		searchBtnCls: 'x-field-search-go',
		onTrigger1Click: this._triggerSearch,
		onTrigger2Click: this._triggerClear,
	});
	LMS.combo.Search.superclass.constructor.call(this, config);
	this.on('render', function() {
		this.getEl().addKeyListener(Ext.EventObject.ENTER, function() {
			this._triggerSearch();
		}, this);
	});
	this.addEvents('clear', 'search');
};
Ext.extend(LMS.combo.Search, Ext.form.TwinTriggerField, {

	initComponent: function() {
		Ext.form.TwinTriggerField.superclass.initComponent.call(this);
		this.triggerConfig = {
			tag: 'span',
			cls: 'x-field-search-btns',
			cn: [
				{tag: 'div', cls: 'x-form-trigger ' + this.searchBtnCls},
				{tag: 'div', cls: 'x-form-trigger ' + this.clearBtnCls}
			]
		};
	},

	_triggerSearch: function() {
		this.fireEvent('search', this);
	},

	_triggerClear: function() {
		this.fireEvent('clear', this);
	},

});
Ext.reg('lms-field-search', LMS.combo.Search);
LMS.panel.CreateProfession = function (config) {
	config = config || {};
	if (!config.id) {
		config.id = 'lms-profession-create';
	}
	Ext.applyIf(config, {
		title: _('company_create'),
		width: '50%',
		autoHeight: true,
		url: LMS.config.connector_url,
		action: 'mgr/profession/create',
		fields: this.getFields(config),
		keys: [{
			key: Ext.EventObject.ENTER, shift: true, fn: function () {
				this.submit()
			}, scope: this
		}]
	});
	LMS.panel.CreateProfession.superclass.constructor.call(this, config);
};
Ext.extend(LMS.panel.CreateProfession, MODx.Window, {

	getFields: function (config) {
		return [{
			xtype: 'textfield',
			fieldLabel: _('profession_name'),
			name: 'name',
			id: config.id + '-name',
			anchor: '99%',
			allowBlank: false,
		}, {
			xtype: 'textarea',
			fieldLabel: _('profession_description'),
			name: 'description',
			id: config.id + '-description',
			height: 150,
			anchor: '99%'
		}];
	},
});
Ext.reg('lms-profession-create', LMS.panel.CreateProfession);

LMS.panel.UpdateProfession = function (config) {
	config = config || {};
	if (!config.id) {
		config.id = 'lms-profession-update';
	}
	Ext.applyIf(config, {
		title: _('company_update'),
		width: 550,
		autoHeight: true,
		url: LMS.config.connector_url,
		action: 'mgr/profession/update',
		fields: this.getFields(config),
		keys: [{
			key: Ext.EventObject.ENTER, shift: true, fn: function () {
				this.submit()
			}, scope: this
		}]
	});
	LMS.panel.UpdateProfession.superclass.constructor.call(this, config);
};
Ext.extend(LMS.panel.UpdateProfession, MODx.Window, {

	getFields: function (config) {
		return [{
			xtype: 'hidden',
			name: 'id',
			id: config.id + '-id',
		}, {
			xtype: 'textfield',
			fieldLabel: _('profession_name'),
			name: 'name',
			id: config.id + '-name',
			anchor: '99%',
			allowBlank: false,
		}, {
			xtype: 'textarea',
			fieldLabel: _('profession_description'),
			name: 'description',
			id: config.id + '-description',
			anchor: '99%',
			height: 150,
		}];
	},

});
Ext.reg('lms-profession-update', LMS.panel.UpdateProfession);
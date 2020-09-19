LMS.window.CreateCompany = function (config) {
	config = config || {};
	if (!config.id) {
		config.id = 'modextra-company-create';
	}
	Ext.applyIf(config, {
		title: _('company_create'),
		width: '50%',
		autoHeight: true,
		url: LMS.config.connector_url,
		action: 'mgr/company/create',
		fields: this.getFields(config),
		keys: [{
			key: Ext.EventObject.ENTER, shift: true, fn: function () {
				this.submit()
			}, scope: this
		}]
	});
	LMS.window.CreateCompany.superclass.constructor.call(this, config);
};
Ext.extend(LMS.window.CreateCompany, MODx.Window, {

	getFields: function (config) {
		return [{
			xtype: 'textfield',
			fieldLabel: _('company_name'),
			name: 'name',
			id: config.id + '-name',
			anchor: '99%',
			allowBlank: false,
			regex:/^[a-zA-Z0-9\s-]*$/,
			regexText: _('company_name_error')
		}, {
			xtype: 'textarea',
			fieldLabel: _('company_description'),
			name: 'description',
			id: config.id + '-description',
			height: 150,
			anchor: '99%'
		}];
	},
});
Ext.reg('modextra-company-create', LMS.window.CreateCompany);

LMS.window.UpdateCompany = function (config) {
	config = config || {};
	if (!config.id) {
		config.id = 'modextra-company-update';
	}
	Ext.applyIf(config, {
		title: _('company_update'),
		width: 550,
		autoHeight: true,
		url: LMS.config.connector_url,
		action: 'mgr/company/update',
		fields: this.getFields(config),
		keys: [{
			key: Ext.EventObject.ENTER, shift: true, fn: function () {
				this.submit()
			}, scope: this
		}]
	});
	LMS.window.UpdateCompany.superclass.constructor.call(this, config);
};
Ext.extend(LMS.window.UpdateCompany, MODx.Window, {

	getFields: function (config) {
		return [{
			xtype: 'hidden',
			name: 'id',
			id: config.id + '-id',
		}, {
			xtype: 'textfield',
			fieldLabel: _('company_name'),
			name: 'name',
			id: config.id + '-name',
			anchor: '99%',
			allowBlank: false,
			regex:/^[a-zA-Z0-9\s-]*$/,
			regexText: _('company_name_error')
		}, {
			xtype: 'textarea',
			fieldLabel: _('company_description'),
			name: 'description',
			id: config.id + '-description',
			anchor: '99%',
			height: 150,
		}];
	},

});
Ext.reg('modextra-company-update', LMS.window.UpdateCompany);
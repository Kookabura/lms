LMS.page.CreateModule = function(config) {
	config = config || {record: {}};
	config.record = config.record || {};
	Ext.applyIf(config, {
		panelXType: 'modx-panel-module',
	});
	config.canDuplicate = false;
	config.canDelete = false;
	LMS.page.CreateModule.superclass.constructor.call(this, config);
};
Ext.extend(LMS.page.CreateModule, MODx.page.CreateResource);
Ext.reg('lms-page-module-create', LMS.page.CreateModule);
LMS.page.UpdateModule = function(config) {
	config = config || {record: {}};
	config.record = config.record || {};
	Ext.applyIf(config, {
		panelXType: 'modx-panel-module'
	});
	config.canDuplicate = false;
	config.canDelete = false;
	LMS.page.UpdateModule.superclass.constructor.call(this, config);
};
Ext.extend(LMS.page.UpdateModule, MODx.page.UpdateResource);
Ext.reg('lms-page-module-update', LMS.page.UpdateModule);
LMS.page.UpdateTest = function(config) {
	config = config || {record: {}};
	config.record = config.record || {};
	Ext.applyIf(config, {
		panelXType: 'modx-panel-test'
	});
	config.canDuplicate = false;
	config.canDelete = false;
	LMS.page.UpdateTest.superclass.constructor.call(this, config);
};
Ext.extend(LMS.page.UpdateTest, MODx.page.UpdateResource);
Ext.reg('lms-page-test-update', LMS.page.UpdateTest);
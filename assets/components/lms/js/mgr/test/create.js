LMS.page.CreateTest = function(config) {
	config = config || {record: {}};
	config.record = config.record || {};
	Ext.applyIf(config, {
		panelXType: 'modx-panel-test',
	});
	config.canDuplicate = false;
	config.canDelete = false;
	LMS.page.CreateTest.superclass.constructor.call(this, config);
};
Ext.extend(LMS.page.CreateTest, MODx.page.CreateResource);
Ext.reg('lms-page-test-create', LMS.page.CreateTest);
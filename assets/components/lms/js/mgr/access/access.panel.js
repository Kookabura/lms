LMS.panel.Access = function(config) {
	config = config || {};
	if (typeof config.standalone == 'undefined') {
		config.standalone = true;
	}

	Ext.applyIf(config, {
		layout: 'column',
		border: false,
		cls:'main-wrapper',
		items: [{
			columnWidth: .5,
			xtype: 'lms-tree-access',
			id: 'lms-tree-comanies',
			standalone: config.standalone,
			parent: config.parent || 0
		}, {
			columnWidth: .5,
			xtype: 'lms-tree-courses'
			,id: 'lms-tree-courses'
			,standalone: config.standalone
			,parent: config.parent || 0
			,enableDrop: false
            ,allowDrop: false
        	,enableDD: false
		}],
		cls: MODx.modx23 ? 'modx23' : 'modx22',
	});
	LMS.panel.Access.superclass.constructor.call(this,config);
};
Ext.extend(LMS.panel.Access, MODx.Panel);
Ext.reg('lms-panel-access', LMS.panel.Access);
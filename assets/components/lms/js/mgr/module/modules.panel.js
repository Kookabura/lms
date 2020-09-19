LMS.panel.Modules = function(config) {
	config = config || {};
	if (typeof config.standalone == 'undefined') {
		config.standalone = true;
	}

	Ext.applyIf(config, {
		layout: 'anchor',
		border: false,
		anchor: '100%',
		items: [{
			xtype: 'lms-grid-modules',
			cls: 'main-wrapper',
			standalone: config.standalone,
			parent: config.parent || 0,
		}],
		cls: MODx.modx23 ? 'modx23' : 'modx22',
	});
	LMS.panel.Modules.superclass.constructor.call(this,config);
};
Ext.extend(LMS.panel.Modules, MODx.Panel);
Ext.reg('lms-panel-modules', LMS.panel.Modules);
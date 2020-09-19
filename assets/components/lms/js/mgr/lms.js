var LMS = function(config) {
	config = config || {};
	LMS.superclass.constructor.call(this,config);
};
Ext.extend(LMS,Ext.Component,{
	page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('lms',LMS);

LMS = new LMS();

LMS.PanelSpacer = {
	html: '<br />',
	cls: 'lms-panel-spacer',
	border: false,
};
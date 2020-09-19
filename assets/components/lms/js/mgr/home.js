LMS.page.Home = function (config) {
	config = config || {};
	Ext.applyIf(config, {
		components: [{
			xtype: 'lms-panel-home',
			renderTo: 'lms-panel-home-div',
			baseCls: 'lms-formpanel',
		}]
	});
	LMS.page.Home.superclass.constructor.call(this, config);
};
Ext.extend(LMS.page.Home, MODx.Component);
Ext.reg('lms-page-home', LMS.page.Home);


LMS.panel.Home = function(config) {
	config = config || {};
	Ext.apply(config, {
		border: false,
		items: [{
			html: '<h2>' + _('lms') + '</h2>',
			border: false,
			cls: 'modx-page-header container',
		}, {
			xtype: 'modx-tabs',
			id: 'lms-home-tabs',
			defaults: {border: false , autoHeight: true},
			border: true,
			stateful: true,
			stateId: 'lms-home-panel',
			stateEvents: ['tabchange'],
			getState: function() {
				return {
					activeTab: this.items.indexOf(this.getActiveTab())
				};
			},
			hideMode: 'offsets',
			items: [{
				title: _('modules'),
				layout: 'anchor',
				items: [{
					html: _('module_lms_intro'),
					border: false,
					bodyCssClass: 'panel-desc',
				},{
					xtype: 'lms-panel-modules',
					preventRender: true,
				}]
			}, {
				title: _('tests'),
				layout: 'anchor',
				items: [{
					html: _('test_lms_intro'),
					border: false,
					bodyCssClass: 'panel-desc',
				},{
					xtype: 'lms-panel-tests',
					preventRender: true,
				}]
			}, {
				title: _('statistic'),
				layout: 'anchor',
				items: [{
					html: _('statistic_lms_intro'),
					border: false,
					bodyCssClass: 'panel-desc',
				},{
					xtype: 'lms-panel-statistic',
					preventRender: true,
				}]
			}, {
				title: _('companies'),
				layout: 'anchor',
				items: [{
					html: _('company_lms_intro'),
					border: false,
					bodyCssClass: 'panel-desc',
				},{
					xtype: 'lms-panel-company',
					preventRender: true,
				}]
			}, {
				title: _('professions'),
				layout: 'anchor',
				items: [{
					html: _('profession_lms_intro'),
					border: false,
					bodyCssClass: 'panel-desc',
				},{
					xtype: 'lms-panel-profession',
					preventRender: true,
				}]
			}, {
				title: _('access'),
				layout: 'anchor',
				items: [{
					html: _('access_lms_intro'),
					border: false,
					bodyCssClass: 'panel-desc',
				},{
					xtype: 'lms-panel-access',
					preventRender: true,
				}]
			}, {
				title: _('report'),
				layout: 'anchor',
				items: [{
					html: _('report_lms_intro'),
					border: false,
					bodyCssClass: 'panel-desc',
				},{
					xtype: 'lms-panel-report',
					preventRender: true,
				}]
			}]
		}]
	});
	LMS.panel.Home.superclass.constructor.call(this,config);
};
Ext.extend(LMS.panel.Home,MODx.Panel);
Ext.reg('lms-panel-home',LMS.panel.Home);
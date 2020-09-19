LMS.page.UpdateCourse = function(config) {
	config = config || {record: {}};
	config.record = config.record || {};
	Ext.applyIf(config, {
		panelXType: 'lms-panel-course-update',
	});
	config.canDuplicate = false;
	config.canDelete = false;
	LMS.page.UpdateCourse.superclass.constructor.call(this, config);
};
Ext.extend(LMS.page.UpdateCourse, MODx.page.UpdateResource);
Ext.reg('lms-page-course-update', LMS.page.UpdateCourse);


LMS.panel.UpdateCourse = function(config) {
	config = config || {};
	LMS.panel.UpdateCourse.superclass.constructor.call(this,config);
};
Ext.extend(LMS.panel.UpdateCourse,MODx.panel.Resource,{

	getFields: function(config) {
		var fields = [];
		var originals = MODx.panel.Resource.prototype.getFields.call(this,config);
		for (var i in originals) {
			if (!originals.hasOwnProperty(i)) {
				continue;
			}
			var item = originals[i];

			if (item.id == 'modx-resource-tabs') {
				item.stateful = true;
				item.stateId = 'lms-course-upd-tabpanel';
				item.stateEvents = ['tabchange'];
				item.getState = function() {
					return {activeTab: this.items.indexOf(this.getActiveTab())};
				};
				var tabs = [];
				for (var i2 in item.items) {
					if (!item.items.hasOwnProperty(i2)) {
						continue;
					}
					var tab = item.items[i2];
					if (tab.id == 'modx-resource-settings') {
						tab.title = _('lms_course');
						tab.items = this.getMainFields(config);
					}
					else if (tab.id == 'modx-page-settings') {
						tab.title = _('lms_course_settings');
						tab.items = this.getCourseSettings(config);
						tab.cls =  'modx-resource-tab';
						tab.bodyCssClass = 'tab-panel-wrapper form-with-labels';
						tab.labelAlign = 'top';
					}
					tabs.push(tab);
				}
				item.items = tabs;
			}
			if (item.id == 'modx-resource-content') {
				fields.push(this.getModules(config));
				fields.push(this.getTests(config));
			}
			else {
				fields.push(item);
			}
		}

		return fields;
	},

	getMainFields: function(config) {
		var fields = MODx.panel.Resource.prototype.getMainFields.call(this, config);
		fields.push({
			xtype: 'hidden',
			name: 'class_key',
			id: 'modx-resource-class-key',
			value: 'Course'
		});
		fields.push({
			xtype: 'hidden',
			name: 'content_type',
			id: 'modx-resource-content-type',
			value: MODx.config['default_content_type'] || 1
		});

		return fields;
	},

	getCourseSettings: function(config) {
		return [{
			xtype: 'lms-course-tab-settings',
			record: config.record,
		}];
	},

	getModules: function(config) {
		return [{
			xtype: 'lms-panel-modules',
			parent: config.resource,
			standalone: false,
		}];
	},

	getTests: function(config) {
		return [{
			xtype: 'lms-panel-tests',
			parent: config.resource,
			standalone: false,
		}];
	},

});
Ext.reg('lms-panel-course-update', LMS.panel.UpdateCourse);

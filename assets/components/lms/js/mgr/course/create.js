LMS.page.CreateCourse = function(config) {
	config = config || {record: {}};
	config.record = config.record || {};
	Ext.applyIf(config, {
		panelXType: 'lms-panel-course-create'
	});
	config.canDuplicate = false;
	config.canDelete = false;
	LMS.page.CreateCourse.superclass.constructor.call(this, config);
};
Ext.extend(LMS.page.CreateCourse, MODx.page.CreateResource);
Ext.reg('lms-page-course-create', LMS.page.CreateCourse);


LMS.panel.CreateCourse = function(config) {
	config = config || {};
	LMS.panel.CreateCourse.superclass.constructor.call(this, config);
};
Ext.extend(LMS.panel.CreateCourse, MODx.panel.Resource, {

	getFields: function(config) {
		var fields = [];
		var originals = MODx.panel.Resource.prototype.getFields.call(this,config);
		for (var i in originals) {
			if (!originals.hasOwnProperty(i)) {
				continue;
			}
			var item = originals[i];

			if (item.id == 'modx-resource-header') {
				item.html = '<h2>' + _('lms_course_new') + '</h2>';
			}
			else if (item.id == 'modx-resource-tabs') {
				item.stateful = true;
				item.stateId = 'lms-course-new-tabpanel';
				item.stateEvents = ['tabchange'];
				item.getState = function() {
					return {activeTab: this.items.indexOf(this.getActiveTab())};
				};
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
						tab.bodyCssClass = 'tab-panel-wrapper';
						tab.labelAlign = 'top';
					}
				}
			}

			if (item.id != 'modx-resource-content') {
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

});
Ext.reg('lms-panel-course-create', LMS.panel.CreateCourse);

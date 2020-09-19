LMS.tree.Access = function(config) {
    config = config || {};
    
    Ext.applyIf(config,{
        url: LMS.config.connector_url
        ,root_id: '0'
        ,root_name: _('companies')
        ,enableDrag: false
        ,action: 'mgr/access/getnodes'
        ,enableDrop: true
        ,ddAppendOnly: true
        ,useDefaultToolbar: true
        ,baseParams: {
            limit: 0
        }
        ,stateful: true
        ,stateId: 'lms-access-state'
    });
    LMS.tree.Access.superclass.constructor.call(this,config);
};
Ext.extend(LMS.tree.Access,MODx.tree.Tree,{
    forms: {}
    ,windows: {}
    ,stores: {}

    ,getMenu: function() {
        var n = this.cm.activeNode;
        var m = [];
        if (n.attributes.type == 'modResourceGroup') {

        } else if (n.attributes.type == 'modResource' || n.attributes.type == 'modDocument') {
            m.push({
                text: _('course_access_remove')
                ,handler: this.removeResource
            });
        }
        return m;
    }

    ,removeResource: function(item,e) {
        var n = this.cm.activeNode;
        var resourceId = n.id.split('_'); resourceId = resourceId[1];
        var resourceGroupId = n.parentNode.id.substr(2).split('_'); resourceGroupId = resourceGroupId[1];

        MODx.msg.confirm({
            text: _('course_access_remove_confirm')
            ,url: MODx.config.connector_url
            ,params: {
                action: 'security/resourcegroup/removeResource'
                ,resource: resourceId
                ,resourceGroup: resourceGroupId
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
	
    ,_handleDrop: function(e){
        var n = e.dropNode;

        if(this.isDocCopy(e,n)) {
            var copy = new Ext.tree.TreeNode(
                Ext.apply({leaf: true,allowDelete:true,expanded:true}, n.attributes)
            );
            copy.loader = undefined;
            if(e.target.attributes.options){
                e.target = this.createDGD(e.target, copy.text);
            }
            e.dropNode = copy;
            return true;
        }
        return false;
    }
	
    ,isDocCopy: function(e, n) {
        var a = e.target.attributes;
        var docid = n.attributes.id.split('_'); docid = 'n_'+docid[1];

        if (e.target.findChild('id',docid) !== null) { return false; }
        if (n.attributes.type != 'modResource' && n.attributes.type != 'modDocument') { return false; }
        if (e.point != 'append') { return false; }
        if (a.type != 'modResourceGroup') { return false; }
        return a.leaf !== true;

    }
	
    ,createDGD: function(n, text){
        var cnode = this.getNodeById(n.attributes.cmpId);

        var node = new Ext.tree.TreeNode({
            text: text
            ,cmpId:cnode.id
            ,leaf: true
            ,allowDelete:true
            ,allowEdit:true
            ,id:this._guid('o-')
        });
        cnode.childNodes[2].appendChild(node);
        cnode.childNodes[2].expand(false, false);

        return node;
    }
    
    ,_handleDrag: function(dropEvent) {
        Ext.Msg.show({
            title: _('please_wait')
            ,msg: _('saving')
            ,width: 240
            ,progress:true
            ,closable:false
        });

        MODx.util.Progress.reset();
        for(var i = 1; i < 20; i++) {
            setTimeout('MODx.util.Progress.time('+i+','+MODx.util.Progress.id+')',i*1000);
        }

        MODx.Ajax.request({
            url: MODx.config.connector_url
            ,scope: this
            ,params: {
                resource: dropEvent.dropNode.attributes.id
                ,resourceGroup: dropEvent.target.attributes.id
                ,action: 'security/resourcegroup/updateResourcesIn'
            }
            ,listeners: {
                'success': {fn: function(r,o) {
                    MODx.util.Progress.reset();
                    Ext.Msg.hide();
                    if (!r.success) {
                        Ext.Msg.alert(_('error'),r.message);
                        return false;
                    }
                    this.refresh();
                    return true;
                },scope:this}
            }
        });
    }
});
Ext.reg('lms-tree-access',LMS.tree.Access);

/**
 * Generates a Simplified Resource Tree in Ext
 *
 * @class LMS.tree.Courses
 * @extends MODx.tree.Tree
 * @param {Object} config An object of options.
 * @xtype modx-tree-resource-simple
 */
LMS.tree.Courses = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        root_id: '0'
        ,url: LMS.config.connector_url
        ,root_name: _('courses')
        ,enableDrag: true
        ,enableDrop: true
        ,action: 'mgr/course/getnodes'
        ,baseParams: {
            nohref: true
        }
    });
    LMS.tree.Courses.superclass.constructor.call(this,config);
};
Ext.extend(LMS.tree.Courses,MODx.tree.Tree,{
    getMenu: function() {
        var n = this.cm.activeNode;
        var m = [];
        if (n.attributes.type == 'modResource') {

        }
        return m;
    }
});
Ext.reg('lms-tree-courses',LMS.tree.Courses);
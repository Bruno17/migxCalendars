<?php

$gridcontextmenus['updateevent']['code']="
        m.push({
            className : 'update', 
            text: '[[%migx.edit]]',
            handler: 'this.updateevent'
        });
        m.push('-');
";
$gridcontextmenus['updateevent']['handler'] = 'this.updateevent';

$gridfunctions['this.updateevent'] = "
updateevent: function(btn,e) {
        var params = {
            event_id: this.menu.record.json['Event_id']
        }
      this.loadWin(btn,e,'u', Ext.util.JSON.encode(params));       
    }
";

$gridfunctions['this.handleEventColumnSwitch'] = "
handleEventColumnSwitch: function(n,e,col) {
    
    var btn,params;
    var column = col;
    //console.log(this.menu.record.json);
    var ro_json = this.menu.record.json[column+'_ro'];
    var ro = Ext.util.JSON.decode(ro_json);
    
        MODx.Ajax.request({
            url: this.config.url
            ,params: {
                action: 'mgr/migxdb/process'
                ,processaction: 'handlecolumnswitch'
                ,col: column
                ,idx: ro.idx
                ,tv_type: this.config.tv_type
                ,object_id: this.menu.record.id
				,configs: this.config.configs
                ,resource_id: this.config.resource_id
                ,event_id: this.menu.record.json['Event_id']
            }
            ,listeners: {
                'success': {fn: function(res){ 
                    this.refresh();
                    
                    },scope:this }
            }
        });
        return false;
    }	
";

$winbuttons['check_availability']['text'] = "'[[%migxcal.check_availability]]'";
$winbuttons['check_availability']['handler'] = 'this.checkAvailability';
$winbuttons['check_availability']['scope'] = 'this';

$winfunctions['this.checkAvailability'] = "
    checkAvailability: function(btn,e) {
        var object_id = this.baseParams.object_id;
        if (this.fp.getForm().isValid()) {
            var v = this.fp.getForm().getValues();
            var fields = Ext.util.JSON.decode(v['mulititems_grid_item_fields']);
            if (fields.length>0){
                for (var i = 0; i < fields.length; i++) {
                    fieldname = (fields[i].field);
                    if (fieldname == 'checkavailability'){
                        tvid = (fields[i].tv_id);
                        grid = Ext.getCmp('tv' + tvid + '_items');
                    }
                    
                }                         
            }
			var item = this.getFormValues();
            grid.loadWin(btn,e,'a',Ext.util.JSON.encode(item));
            return true;        
        }            
        return false;
    }
";


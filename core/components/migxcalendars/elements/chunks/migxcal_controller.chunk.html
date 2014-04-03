var migxcalController = null;
var migxcal_dragstart_day = null;

jQuery(document).ready(function() {
   migxcalController = $('#migxcalCtrl').scope();
   
    $('.migxcal_category').draggable({
        zIndex: 999,
        revert: true,      // immediately snap back to original position
        revertDuration: 0  //
    });   

});

function migxcalCtrl($scope, $http, Config, UiDialog) {
    $scope.config = Config;
    $scope.eventResize = function(event,revertFunc){
        /*
        if (!confirm("is this okay?")) {
            revertFunc();
            return;
        } 
        */
        $scope.revertFunc = revertFunc;
        event.allDay = event.allDay ? '1' : '0';       
        $scope.changeEventDates(event,'update');
    }
    $scope.eventDrop = function(event,revertFunc,jsEvent, ui, view){
        
        var day = event.start.dayOfYear();
        var week = event.start.isoWeek();
        /*                
        if (event.Event_repeating == '1' && migxcal_dragstart_day != day && !confirm("[[%migxcal.warn_move_repeat_2other_day]]")) {
            //move to other day
            revertFunc();
            return;
        }
        */
        if (event.Event_repeating == '1' && migxcal_dragstart_week != week ) {
            //move to other week
            alert("[[%migxcal.warn_move_repeat_2other_week]]");
            revertFunc();
            return;
        }          
        
        $scope.revertFunc = revertFunc;       
        event.allDay = event.allDay ? '1' : '0'; 
        $scope.changeEventDates(event,'update');
    }
    
    $scope.eventEdit = function(data){
        
        var event_id = data.id || 0; 
        $scope.revertFunc = function(){};        
        $scope.editEvent(event_id);        
    } 
    
    $scope.eventPublish = function(data){
        
        var event_id = data.id || 0;        
        var cfg = Config;
        cfg.method = 'POST';
        var dialogOptions = {};
        var params = {};

        params.configs = 'migxcalendar_dragdropdate';
        params.action = 'mgr/migxdb/process';
        params.processaction = 'publishdate';
        params.object_id = event_id;
        //params.original_request_uri = request_uri;
        params.data = data;        
        
        var ajaxConfig = UiDialog.preparePostParams(cfg, params);
        ajaxConfig.data = {
            //data: data
        };

        $http(ajaxConfig).success(function(response, status, header, config) {
           
            if (response){
            var success = response.success || false;
            var message = response.message || '';
            
            if (success) {

            } else {
                alert(message);
            }                
            }
            $scope.refresh();

        }).error(function(data, status, header, config) {
            //UiDialog.error(data, status, header, config);
        });        
               
    }        
    
    $scope.eventDropNew = function(event,el){
        
        /*
        if (!confirm("is this okay?")) {
            revertFunc();
            return;
        } 
        */
        if (el.scope){
            var datecontainer = el.scope().date_container;
            if (datecontainer && datecontainer.repeating == '1'){
                alert("[[%migxcal.warn_dragnew_repeatingdate]]");
                return;    
            } 
        }
        
        $scope.revertFunc = function(){
            return;
        };       
        $scope.changeEventDates(event);
    }    
    
    $scope.changeEventDates = function(event,action){
        var end = '';
        var start = '';
        var action = action || 'edit';
        
        if (event.end){
            end =  event.end.format();   
        }
        if (event.start){
            start =  event.start.format();   
        }
        var data = {
            enddate : end,
            startdate : start
        };
        if (event.allDay){
            data.allday = event.allDay;
        }        
        if (event.data){
            if (event.data.id){
               data.Event_categoryid = event.data.id; 
            }            
            if (event.data.catid){
               data.Event_categoryid = event.data.catid; 
            }
            if (event.data.eventid){
               data.event_id = event.data.eventid; 
            }            
            
        }
        
        var event_id = event.id || 'new';
        
        if (action == 'edit'){
            $scope.editEvent(event_id , angular.toJson(data));  
        }
        if (action == 'update'){
            $scope.updateEvent(event_id , angular.toJson(data));  
        }        
                  
    }
    
    $scope.hidePleaseWait = function(){
        if ($scope.waiting){
            UiDialog.hidePleaseWait(); 
            $scope.waiting = false;     
        }
    }
    
    $scope.updateEvent = function(event_id,data) {
        var cfg = Config;
        cfg.method = 'POST';
        
        var params = {
            'configs':'migxcalendar_dragdropdate',
            'object_id':event_id,
            'action':'mgr/migxdb/update'
        };
        
        var ajaxConfig = UiDialog.preparePostParams(cfg, params);
        ajaxConfig.data = {
            data : data 
        };
        UiDialog.showPleaseWait();
        $http(ajaxConfig).success(function(response, status, header, config) {
            $scope.refresh();
            //UiDialog.hidePleaseWait();
            $scope.waiting = true;
        }).error(function(data, status, header, config) {
            UiDialog.error(data, status, header, config);
        });        
    }
    
    $scope.editEvent = function(event_id,data) {
        var cfg = Config;
        cfg.method = 'POST';

        var dialogOptions = {};

        var params = {};

        params.configs = 'migxcalendar_dragdropdate';
        //params.action = 'mgr/migxdb/process';
        params.action = 'web/migxdb/fields';
        //params.processaction = 'updateevent';
        params.object_id = event_id;
        //params.original_request_uri = request_uri;
        params.data = data;
        
        UiDialog.loadModal($scope, Config, params, dialogOptions);
        
        return; 
        
    } 
    
    $scope.refresh = function(){
        $('#calendar').fullCalendar('refetchEvents');
        $scope.relaodDateContainers();    
    }
    
    $scope.closecontainer = function(datecontainer){
        console.log($scope.date_containers['dc_'+datecontainer.id]);
        console.log($scope.date_containers);
        
        if (datecontainer.id){
            delete $scope.date_containers['dc_'+datecontainer.id];
        }
        
        
        

        
        
    }
    
    $scope.relaodDateContainers = function(){
        $('.datecontainer').each(function(){
            var data = $(this).data();
            if (data.eventid){
                $scope.loadDatesContainer(data.eventid);    
            }
        })
        
        
                    
    }
    
    $scope.loadDatesContainer = function(event_id){
       
            var cfg = Config;
            cfg.method = 'POST';
            var dialogOptions = {};
            var params = {};

            params.configs = 'migxcalendar_loadcontainer';
            params.action = 'mgr/migxdb/process';
            params.processaction = 'loadcontainer';
            params.object_id = event_id;
            //params.original_request_uri = request_uri;
            //params.data = data;        
        
            var ajaxConfig = UiDialog.preparePostParams(cfg, params);
            ajaxConfig.data = {
                //data: data
            };

            $http(ajaxConfig).success(function(response, status, header, config) {
           
                if (response){
                    var success = response.success || false;
                    var message = response.message || '';
            
                    if (success && response.object) {
                        data = {};
                        data.item = response.object;
                        $scope.setDatesContainer(data);
                    } else {
                        alert(message);
                        return;
                    }                
                }
                //$('#calendar').fullCalendar( 'refetchEvents' );

            }).error(function(data, status, header, config) {
                //UiDialog.error(data, status, header, config);
            });                   
    }
    
    $scope.setDatesContainer = function(data){
        $scope.date_containers = $scope.date_containers || {};
        if (typeof(data.item) != 'undefined'){
            $scope.date_containers['dc_' + data.item.id] = data.item;
            
            setTimeout(function(){
                $scope.$apply();
                //style="color:[[+textColor:default=``]];background-color:[[+backgroundColor:default=``]];"
                var color = data.item.Category_textColor && data.item.Category_textColor != '' ? data.item.Category_textColor : '#FFFFFF';
                var background_color = data.item.Category_backgroundColor && data.item.Category_backgroundColor != '' ? data.item.Category_backgroundColor : '#3A87AD';
            
                $('#datecontainer'+data.item.id).css({
                    'color' : color,
                    'background-color' : background_color
                }).draggable({
                    zIndex: 999,
                    revert: true,      // immediately snap back to original position
                    revertDuration: 0  //                
                });                
            },100);
        }
        return;
    }   
        
}   


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
            $('#calendar').fullCalendar( 'refetchEvents' );

        }).error(function(data, status, header, config) {
            //UiDialog.error(data, status, header, config);
        });        
               
    }        
    
    $scope.eventDropNew = function(event){
        
        /*
        if (!confirm("is this okay?")) {
            revertFunc();
            return;
        } 
        */
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
        if (event.category_id){
            data.Event_categoryid = event.category_id;
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
            $('#calendar').fullCalendar('refetchEvents');
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
        
}   


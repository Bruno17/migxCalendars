var migxcalController = null;

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
        $scope.changeEventDates(event);
    }
    $scope.eventDrop = function(event,revertFunc,jsEvent, ui, view){
        
        /*
        if (!confirm("is this okay?")) {
            revertFunc();
            return;
        } 
        */
        $scope.revertFunc = revertFunc;       
        event.allDay = event.allDay ? '1' : '0'; 
        $scope.changeEventDates(event);
    }
    
    $scope.eventEdit = function(data){
        
        var event_id = data.id || 0;        
        $scope.editEvent(event_id);        
    } 
    
    $scope.eventPublish = function(data){
        
        var event_id = data.id || 0;        
        console.log(data); 
        
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
    
    $scope.changeEventDates = function(event){
        var end = '';
        var start = '';
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
        $scope.editEvent(event_id , angular.toJson(data));            
        
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
        
        var ajaxConfig = UiDialog.preparePostParams(cfg, params);
        ajaxConfig.data = {
            data: data
        };

        $http(ajaxConfig).success(function(response, status, header, config) {
            $('#calendar').fullCalendar( 'refetchEvents' );
            if (response){
            var success = response.success || false;
            var message = response.message || '';
            console.log('test');
            
            if (success) {
                
                /*
                var data = success.data || {};
                var categories = data.categories || false;
                var courts = data.courts || false;
                */
                
                /* 
                if (categories) {
                    $scope.categories = angular.fromJson(categories);
                }
                if (courts) {
                    $scope.courts = angular.fromJson(courts);
                }
                $scope.formated_date = data.formated_date || 'date-error';
                */
            } else {
                //alert(message);
            }                
            }


        }).error(function(data, status, header, config) {
            UiDialog.error(data, status, header, config);
        });
    }    
        
}   


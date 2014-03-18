var migxcalController = null;

jQuery(document).ready(function() {
   migxcalController = $('#migxcalCtrl').scope();

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
        $scope.changeEventDates(event);
    }
    $scope.eventDrop = function(event,revertFunc,jsEvent, ui, view){
        
        /*
        if (!confirm("is this okay?")) {
            revertFunc();
            return;
        } 
        */       
        $scope.changeEventDates(event);

    }
    
    $scope.changeEventDates = function(event){
        var end = '';
        if (event.end){
            end =  event.end.format();   
        }
        var start = '';
        if (event.start){
            start =  event.start.format();   
        }        
        var data = {
            enddate : end,
            startdate : start,
            allday : event.allDay
        };
        console.log(event);
        console.log(end);
        console.log(event.start.format());
        $scope.editEvent(event.id , angular.toJson(data));            
        
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
                alert(message);
            }                
            }


        }).error(function(data, status, header, config) {
            UiDialog.error(data, status, header, config);
        });
    }    
        
}   


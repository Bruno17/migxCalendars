
<button type="button" ng-click = "onMigxcalDoneCloseButtonClick({'closeonsuccess':true,'configs':'[[+request.configs]]','object_id':'[[+request.object_id]]','resource_id':'[[+request.resource_id]]','wctx':'[[+request.wctx]]','field':'[[+request.field]]','action':'mgr/migxdb/update','processaction':''})" class="btn btn-default" role="button" aria-disabled="false">
[[%save_and_close]]
</button>

<script>

    $scope.onMigxcalDoneCloseButtonClick = function(params) {
        var cfg = Config;
        cfg.method = 'POST';
        var ajaxConfig = UiDialog.preparePostParams(cfg, params);
        ajaxConfig.data = {
            data : angular.toJson($scope.data) 
        };
        if ($scope.data.old_startdate != ''){
            if ($scope.data.Event_repeating != $scope.data.Event_repeating_old && !confirm("[[%migxcal.warn_change_repeating]]")){
                //UiDialog.hideModal('[[+request.modal_id]]');
                return;    
            }
            if ($scope.data.Event_repeating == 1){
                var old_startdate = moment($scope.data.old_startdate);
                if (old_startdate){
                    var old_startweek = old_startdate.isoWeek();
                }
                var startdate = moment($scope.data.startdate);
                if (startdate){
                    var startweek = startdate.isoWeek();
                }            
                if (startweek != old_startweek ){
                    //UiDialog.hideModal('[[+request.modal_id]]');
                    alert("[[%migxcal.warn_move_repeat_2other_week]]");
                    return;    
                }
            }            
        }
        
        $http(ajaxConfig).success(function(response, status, header, config) {
            if (params.closeonsuccess){
                UiDialog.hideModal('[[+request.modal_id]]');
            }
            $scope.refresh();
        }).error(function(data, status, header, config) {
            UiDialog.error(data, status, header, config);
        });
        
        //dialog.dialog('close');
    }
    
</script>        
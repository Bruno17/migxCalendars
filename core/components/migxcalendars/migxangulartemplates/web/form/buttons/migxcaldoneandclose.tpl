
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
        $http(ajaxConfig).success(function(response, status, header, config) {
            if (params.closeonsuccess){
                UiDialog.hideModal('[[+request.modal_id]]');
            }
            $('#calendar').fullCalendar('refetchEvents');
        }).error(function(data, status, header, config) {
            UiDialog.error(data, status, header, config);
        });
        
        //dialog.dialog('close');
    }
    
</script>        
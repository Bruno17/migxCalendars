
<button type="button" ng-click = "onMigxcalLoadDateContainerClick()" class="btn btn-default" role="button" aria-disabled="false">
[[%migxcal.load_datecontainer]]
</button>

<script>

    $scope.onMigxcalLoadDateContainerClick = function(params) {

        $scope.setDatesContainer($scope.data);
        UiDialog.hideModal('[[+request.modal_id]]'); 

    }
    
</script>        
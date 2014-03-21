
<button type="button" ng-click = "OnMigxcalCancelButtonClick()"  class="btn btn-default" role="button" aria-disabled="false">
[[%cancel]]
</button>

<script>
    $scope.OnMigxcalCancelButtonClick = function(config) {
        UiDialog.hideModal('[[+request.modal_id]]');
        $scope.revertFunc();
    }
</script>
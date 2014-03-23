<div class="form-group">
<label for="tv[[+tv.id]]">
[[+tv.caption]]
</label>

<div class="livesearchitem-content active">
<strong>{{selected[[+tv.fieldname]].id}}</strong><br />  
{{selected[[+tv.fieldname]].title}}<br /> 
{{selected[[+tv.fieldname]].fullname}} 
</div>

<div class="form-group">
<label for="selectedCategory">
Buchungstyp Filter
</label>

<select id="selectedBookingType" ng-change="changeBookingType[[+tv.fieldname]]()" class="form-control" ng-model="bookingtype[[+tv.fieldname]]" ng-options="i.alias as i.name for i in bookingtypes[[+tv.fieldname]]"></select>

</div>

<div class="input-group">
<input class="form-control" 
  ng-change="onLiveSearchChange[[+tv.fieldname]]()" 
  ng-model="data.[[+tv.fieldname]]_term"
  ng-keydown="onKeyDown[[+tv.fieldname]]($event)" 
  ng-blur="onBlur[[+tv.fieldname]]()"
  ng-focus="onFocus[[+tv.fieldname]]()"
  id="tv[[+tv.id]]" name="tv[[+tv.id]]" type="text" 
/>
<span class="input-group-addon glyphicon glyphicon-search"></span>
</div>

<div class="btn-group">
  <button ng-click="changePage[[+tv.fieldname]]()" ng-repeat="page in pages[[+tv.fieldname]]" class="btn {{page.btn_class}}" ng-bind-html="page.label"></button>
</div>

<div class="livesearch-items" >
<table>
<thead>
<th style="width: 40px;">ID</th>
<th>Erstellt am</th>
<th>Label</th>
<th>Name</th>
</thead>
<tbody>
<tr class="livesearchitem livesearchitem-content {{result.activeclass}}" ng-mouseover="onMouseOver[[+tv.fieldname]]()" ng-click="onClick[[+tv.fieldname]]()" ng-repeat="result in result[[+tv.fieldname]]" >
<td><strong>{{result.id}} </strong></td>
<td>{{result.createdon_formated}}</td>
<td>{{result.title}}</td>
<td>{{result.fullname}}</td>
</tr>
</tbody>
</table>

<hr />
</div> 

</div>

<script>
//this part gets inserted into the formCtrl[[+request.dialogCounter]] - controller

    $scope.[[+tv.fieldname]]_is_searching = false;
    $scope.[[+tv.fieldname]]_query = '';
    
    $scope.changeBookingType[[+tv.fieldname]] = function(){
        $scope.resetPage[[+tv.fieldname]]();
        $scope.refresh[[+tv.fieldname]](); 
    }    
    
    $scope.onBlur[[+tv.fieldname]] = function(){
        $timeout(function() { $scope.showitems[[+tv.fieldname]]=false; }, 100);
        
    }
    
    $scope.onFocus[[+tv.fieldname]] = function(){
        $scope.showitems[[+tv.fieldname]]=true; 
    }    
    
    $scope.onMouseOver[[+tv.fieldname]] = function(){
        var items = $scope.result[[+tv.fieldname]];
        var index = this.$index;       
        var item = items[index];
        $scope.activate[[+tv.fieldname]](item); 
    } 
    
    $scope.onClick[[+tv.fieldname]] = function(){
        $scope.showitems[[+tv.fieldname]]=true;
        var items = $scope.result[[+tv.fieldname]];
        var index = this.$index;
        var item = items[index];
        $scope.select[[+tv.fieldname]](item);   
    } 
    
    $scope.onKeyDown[[+tv.fieldname]] = function(e){
        //activate prev/next
        if (e.keyCode === 40 || e.keyCode === 38){
            e.preventDefault(); 
            $scope.showitems[[+tv.fieldname]]=true;
            var items = $scope.result[[+tv.fieldname]];
            var index = items.indexOf($scope.active[[+tv.fieldname]]);
            if (e.keyCode === 40) {
                //scope.$apply(function() { controller.activateNextItem(); });
                var item = items[(index + 1) % items.length];
            }

            if (e.keyCode === 38) {
                var item = items[index === 0 ? items.length - 1 : index - 1]
               //scope.$apply(function() { controller.activatePreviousItem(); });
            }
            $scope.activate[[+tv.fieldname]](item);                      
        }
        
        if (e.keyCode === 9 || e.keyCode === 13) {
            $scope.select[[+tv.fieldname]]($scope.active[[+tv.fieldname]]);
        }        
        
    }
    
    $scope.activate[[+tv.fieldname]] = function(item) {
        var items = $scope.result[[+tv.fieldname]];
        for (var i = 0; i < items.length; i++) {
            items[i].activeclass = '';
        }
        
        item.activeclass = 'active';
        $scope.active[[+tv.fieldname]] = item;
        
    };    

    $scope.select[[+tv.fieldname]] = function(item){
        $scope.selected[[+tv.fieldname]] = item;
        $scope.data.[[+tv.fieldname]] = item['id']; 
        $scope.showitems[[+tv.fieldname]]=false;         
    }
    
    $scope.onLiveSearchChange[[+tv.fieldname]] = function(){
        
        if ($scope.[[+tv.fieldname]]_is_searching){
            return;
        }
        
        $scope.[[+tv.fieldname]]_query = $scope.data.[[+tv.fieldname]]_term;
        $scope.resetPage[[+tv.fieldname]]();
        $scope.refresh[[+tv.fieldname]]();

        
    }
    
    $scope.refresh[[+tv.fieldname]] = function(){
        var params = {
            'action':'mgr/migxdb/process',
            'configs':'[[+request.configs]]',
            'limit':$scope.pageSize[[+tv.fieldname]],
            'start':$scope.PageStart[[+tv.fieldname]],
            'processaction':'[[+params.processaction:isnot=``:then=`[[+params.processaction]]`:else=`getlivesearch`]]',
            'query':$scope.data.[[+tv.fieldname]]_term,
            'bookingtype' : $scope.bookingtype[[+tv.fieldname]]
            
        }
        
        var cfg = Config;
        //cfg.method = 'POST';
        var ajaxConfig = UiDialog.prepareFormParams(cfg, params);
        ajaxConfig.url = Config.migxurl;
        $scope.[[+tv.fieldname]]_is_searching = true;
        $http(ajaxConfig).success(function(response, status, header, config) {
            $scope.liveSearchCheckNewSearch[[+tv.fieldname]]();
            $scope.result[[+tv.fieldname]] = response.results;
            //$scope.showitems[[+tv.fieldname]]=true;
            var total = response.total || 0;
            $scope.generatePages[[+tv.fieldname]](total);            
            
        }).error(function(data, status, header, config) {
            $scope.liveSearchCheckNewSearch[[+tv.fieldname]]();
            UiDialog.error(data, status, header, config);
            
        });        
    }
    
    $scope.changePage[[+tv.fieldname]] = function(){
        var page = $scope.pages[[+tv.fieldname]][this.$index].number;
        var start = $scope.pages[[+tv.fieldname]][this.$index].start;
        if (page){
          $scope.currentPage[[+tv.fieldname]] = page;
          $scope.PageStart[[+tv.fieldname]] = start;
          $scope.refresh[[+tv.fieldname]]();                
        }
    } 
    
    $scope.resetPage[[+tv.fieldname]] = function(){
          $scope.currentPage[[+tv.fieldname]] = 1;
          $scope.PageStart[[+tv.fieldname]] = 0;        
    }       
    
    $scope.generatePages[[+tv.fieldname]] = function(totalItems){
        var currentPage = $scope.currentPage[[+tv.fieldname]] || 1;
        var pageSize = $scope.pageSize[[+tv.fieldname]] || 10;
        var pages = UiDialog.generatePages(currentPage, totalItems, pageSize,{'showFirst':false,'showLast':false});
        $scope.pages[[+tv.fieldname]] = pages;
    }    
    
    $scope.liveSearchCheckNewSearch[[+tv.fieldname]] = function(){
        $scope.[[+tv.fieldname]]_is_searching = false;
        if ($scope.[[+tv.fieldname]]_query != $scope.data.[[+tv.fieldname]]_term){
            $scope.onLiveSearchChange[[+tv.fieldname]]();    
        }            
    } 
    
    $scope.pageSize[[+tv.fieldname]] = 10;
    //$scope.bookingtypes[[+tv.fieldname]] = angular.fromJson('[[+bookingtypes]]');
    $scope.bookingtype[[+tv.fieldname]] = '';
    
    $scope.resetPage[[+tv.fieldname]]();
    $scope.onLiveSearchChange[[+tv.fieldname]]();   

</script>
<?php
$lang = $modx->getOption('lang', $scriptProperties, $modx->getOption('cultureKey'));
$editable = $modx->getOption('editable', $scriptProperties, 'false');
$aspectRatio = (float)$modx->getOption('aspectRatio', $scriptProperties, '0.4');
$minTime = $modx->getOption('minTime', $scriptProperties, '6:00');
$defaultView = $modx->getOption('defaultView', $scriptProperties, 'agendaWeek');

$ajax_id = $modx->getOption('ajax_id', $scriptProperties, $modx->getOption('migxcalendar.ajax_id'));
$ajax_url = $modx->makeUrl($ajax_id);

$load_jquery = $modx->getOption('load_jquery', $scriptProperties, '1');
$load_jqueryui = $modx->getOption('load_jqueryui', $scriptProperties, '1');
$load_bootstrap = $modx->getOption('load_bootstrap', $scriptProperties, '1');
$load_fullcalendar = $modx->getOption('load_fullcalendar', $scriptProperties, '1');

$packageName = $modx->getOption('packageName', $scriptProperties, 'migxcalendars');

$params = $_REQUEST;

$categories = $modx->getOption('categories', $params, '');
$date = $modx->getOption('date', $params, '');
$view = $modx->getOption('view', $params, $defaultView);

$scriptProperties['currentUrl'] = $modx->makeUrl($modx->resource->get('id'));

if (!empty($date)) {
    $defaultDate = "defaultDate : '" . $date . "',";
}

$extraOptionsTpl = $modx->getOption('extraOptionsTpl', $scriptProperties, '');

$extraOptions = !empty($extraOptionsTpl) ? ',' . $modx->getChunk($extraOptionsTpl,$scriptProperties) : '';

if ($modx->lexicon) {
    $modx->lexicon->load($packageName . ':default');
}

if (!empty($load_fullcalendar)) {
    $script = "
<script>
var initialLoad = true;    
var poppingState = false;

window.onpopstate = function(event){
    if (event.state.start){
        if ( typeof migxcalController != 'undefined' ){
            migxcalController.config.baseParams.original_request_uri = event.state.url; 
        }
        poppingState = true; //don't re-push state
        $('#calendar').fullCalendar('gotoDate', event.state.start);
        poppingState = true; //don't re-push state
        $('#calendar').fullCalendar('changeView', event.state.viewMode);

    }

}

if (window.history){
    var windowHistory = window.history;
    // symlink to method 'history.pushState'
    var historyPushState = windowHistory.pushState;
    // if the browser supports HTML5-History-API
    var isSupportHistoryAPI = !!historyPushState; 
}

var migxcal_history = function(view,element,url){

    // Prevent the code from running if there is no window.history object
    if (!window.history) return;
    var date_param = '?date=' + view.intervalStart.format('YYYY-MM-DD');
    var view_param = '&view=' + view.name;
    
    url = isSupportHistoryAPI ? url : '';
    url = url + date_param + view_param;

        
    if (initialLoad) { //Replace the current state to set up state variables.  URL should be identical
        history.replaceState({ viewMode:view.name, start:view.intervalStart, url:url }, 'Edit Calendar', url);
        if ( typeof migxcalController != 'undefined' ){
            migxcalController.config.baseParams.original_request_uri = url; 
        }
        initialLoad = false;
    }else{
        if (!poppingState) { 
            history.pushState({ viewMode:view.name, start:view.intervalStart, url:url }, 'Edit Calendar', url); 
            if (typeof migxcalController != 'undefined'){
                migxcalController.config.baseParams.original_request_uri = url; 
            }
        }else{
            poppingState = false;
    }
}

};

	$(document).ready(function() {
	    var migxcal_categories = {};
        
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			{$defaultDate}
			lang: '{$lang}',
            editable: {$editable},
            aspectRatio: {$aspectRatio},
            minTime: '{$minTime}',
            defaultView:'{$view}',
			events: {
				url: '{$ajax_url}',
                data: function() {
                    return {categories:migxcal_categories};
                },                
				error: function() {
					$('#script-warning').show();
				}
			},
			loading: function(bool) {
				$('#loading').toggle(bool);
			}
            {$extraOptions}
            
		});
        $('.migxcal_category').click(function(){
            var el = $(this);
            var id = el.data().id;
            el.toggleClass('selected');
            if (el.hasClass('selected')){
                migxcal_categories['c_' + id] = id; 
            }
            else{
                migxcal_categories['c_' + id] = 0; 
            }
            $('#calendar').fullCalendar( 'refetchEvents' );
        });
		
	});

</script>
";


    $modx->regClientCSS('assets/components/migxcalendars/js/fullcalendar/fullcalendar.css');
    $modx->regClientStartupHTMLBlock('<link type="text/css" href="assets/components/migxcalendars/js/fullcalendar/fullcalendar.print.css" rel="stylesheet" media="print">');
}

if (!empty($load_jquery)) {
    $modx->regClientScript('assets/components/migxcalendars/js/lib/jquery.min.js');
}
if (!empty($load_jquery)) {
    $modx->regClientScript('assets/components/migxcalendars/js/lib/jquery-ui.custom.min.js');
}

if (!empty($load_bootstrap)) {
    $modx->regClientScript('assets/components/migxangular/bootstrap-3.0.0/js/bootstrap.min.js');
    $modx->regClientCSS('assets/components/migxangular/bootstrap-3.0.0/css/bootstrap.custom.css');
}
$modx->regClientCSS('assets/components/migxcalendars/css/style.css');

if (!empty($load_fullcalendar)) {
    $modx->regClientScript('assets/components/migxcalendars/js/lib/moment.min.js');
    $modx->regClientScript('assets/components/migxcalendars/js/fullcalendar/fullcalendar.min.js');
    $modx->regClientScript('assets/components/migxcalendars/js/fullcalendar/lang/' . $lang . '.js');
    $modx->regClientHTMLBlock($script);
}
$modx->regClientScript('assets/components/migxcalendars/js/history/history.min.js');
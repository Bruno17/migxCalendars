$lang = $modx->getOption('lang',$scriptProperties,$modx->getOption('cultureKey'));
$editable = $modx->getOption('editable',$scriptProperties,'false');
$aspectRatio = (float) $modx->getOption('aspectRatio',$scriptProperties,'0.4');
$minTime = $modx->getOption('minTime',$scriptProperties,'6:00');
$defaultView = $modx->getOption('defaultView',$scriptProperties,'agendaWeek');

$ajax_id = $modx->getOption('ajax_id',$scriptProperties,$modx->getOption('migxcalendar.ajax_id'));
$ajax_url = $modx->makeUrl($ajax_id);

$load_jquery = $modx->getOption('load_jquery',$scriptProperties,'1');
$load_jqueryui = $modx->getOption('load_jqueryui',$scriptProperties,'1');
$load_bootstrap = $modx->getOption('load_bootstrap',$scriptProperties,'1');
$packageName = $modx->getOption('packageName',$scriptProperties,'migxcalendars');

$params = $_REQUEST;

$categories = $modx->getOption('categories',$params,'');
$date = $modx->getOption('date',$params,'');

if (!empty($date)){
    $defaultDate = "defaultDate : '". $date ."',";
}

$extraOptionsTpl = $modx->getOption('extraOptionsTpl',$scriptProperties,'');

$extraOptions = !empty($extraOptionsTpl) ? ',' . $modx->getChunk($extraOptionsTpl) : '';

if ($modx->lexicon) {
    $modx->lexicon->load($packageName . ':default');
}

$script = "
<script>

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
            defaultView:'{$defaultView}',
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
$modx->regClientCSS('assets/components/migxcalendars/css/style.css');
$modx->regClientStartupHTMLBlock('<link type="text/css" href="assets/components/migxcalendars/js/fullcalendar/fullcalendar.print.css" rel="stylesheet" media="print">');
$modx->regClientScript('assets/components/migxcalendars/js/lib/moment.min.js');
if (!empty($load_jquery)){
    $modx->regClientScript('assets/components/migxcalendars/js/lib/jquery.min.js');    
}
if (!empty($load_jquery)){
    $modx->regClientScript('assets/components/migxcalendars/js/lib/jquery-ui.custom.min.js');
}

if (!empty($load_bootstrap)){
    $modx->regClientScript('assets/components/migxangular/bootstrap-3.0.0/js/bootstrap.min.js');
    $modx->regClientCSS('assets/components/migxangular/bootstrap-3.0.0/css/bootstrap.custom.css');
}


$modx->regClientScript('assets/components/migxcalendars/js/fullcalendar/fullcalendar.min.js');
$modx->regClientScript('assets/components/migxcalendars/js/fullcalendar/lang/de.js');
$modx->regClientHTMLBlock($script);
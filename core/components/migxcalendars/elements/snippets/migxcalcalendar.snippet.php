$lang = $modx->getOption('lang',$scriptProperties,$modx->getOption('cultureKey'));
$editable = $modx->getOption('editable',$scriptProperties,'false');
$ajax_id = $modx->getOption('ajax_id',$scriptProperties,$modx->getOption('migxcalendar.ajax_id'));
$ajax_url = $modx->makeUrl($ajax_id);

$script = "
<script>

	$(document).ready(function() {
	
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			//defaultDate: '2014-01-12',
			lang: '{$lang}',
            editable: {$editable},
			events: {
				url: '{$ajax_url}',
				error: function() {
					$('#script-warning').show();
				}
			},
			loading: function(bool) {
				$('#loading').toggle(bool);
			}
		});
		
	});

</script>
";

$modx->regClientCSS('assets/components/migxcalendars/js/fullcalendar/fullcalendar.css');
$modx->regClientStartupHTMLBlock('<link type="text/css" href="assets/components/migxcalendars/js/fullcalendar/fullcalendar.print.css" rel="stylesheet" media="print">');
$modx->regClientScript('assets/components/migxcalendars/js/lib/moment.min.js');
$modx->regClientScript('assets/components/migxcalendars/js/lib/jquery.min.js');
$modx->regClientScript('assets/components/migxcalendars/js/lib/jquery-ui.custom.min.js');
$modx->regClientScript('assets/components/migxcalendars/js/fullcalendar/fullcalendar.min.js');
$modx->regClientScript('assets/components/migxcalendars/js/fullcalendar/lang/de.js');
$modx->regClientScript('');
$modx->regClientHTMLBlock($script);
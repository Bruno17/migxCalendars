<?php
$scriptTpl = $modx->getOption('scriptTpl', $scriptProperties, 'migxcal_script_fullcalendar');
$scriptProperties['lang'] = $lang = $modx->getOption('lang', $scriptProperties, $modx->getOption('cultureKey'));
$scriptProperties['editable'] = $modx->getOption('editable', $scriptProperties, 'false');
$scriptProperties['aspectRatio'] = (float)$modx->getOption('aspectRatio', $scriptProperties, '0.4');
$scriptProperties['minTime'] = $modx->getOption('minTime', $scriptProperties, '6:00');
$scriptProperties['defaultView'] = $modx->getOption('defaultView', $scriptProperties, 'agendaWeek');
$scriptProperties['defaultDate'] = $modx->getOption('defaultDate', $scriptProperties, '');

$scriptProperties['$ajax_id'] = $modx->getOption('ajax_id', $scriptProperties, $modx->getOption('migxcalendar.ajax_id'));
$scriptProperties['ajax_url'] = $modx->makeUrl($ajax_id);

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

$scriptProperties['extraOptions'] = !empty($extraOptionsTpl) ? ',' . $modx->getChunk($extraOptionsTpl,$scriptProperties) : '';

if ($modx->lexicon) {
    $modx->lexicon->load($packageName . ':default');
}

if (!empty($load_fullcalendar)) {
    $script = $modx->getChunk($scriptTpl, $scriptProperties);


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
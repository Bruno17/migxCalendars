<?php

$scriptProperties['perPage'] = 5; //Anzahl der anzuzeigenden Events im kommende-Termine-Modus
$scriptProperties['custom']['docids']['detail'] = '7'; //SeitenID der migxCalendars-Detailseite

if (isset($_REQUEST['category'])) {
    $category = $_REQUEST['category']; //filtern nach Kategorie
} elseif (isset($scriptProperties['category'])){
    $category = $scriptProperties['category'];    
}

$scriptProperties['custom']['category'] = isset($category) ? $category : '0';

//$includes='xettcal'; //um die xettcal.class.inc.php einzubinden

/*
if (isset($_REQUEST['timeID'])){
$task='termin_details';
}
*/
//$task=isset($_REQUEST['task'])?$_REQUEST['task']:$task;a
//$scriptProperties['includes'] = $includes;

$scriptProperties['project'] = 'migxcalendars'; //Projektverzeichnis unter core/components/{component}/bloxprojects/{project}/
$scriptProperties['task'] = 'upcoming'; //Taskverzeichnis core/components/{component}/bloxprojects/{project}/{task}/

// Einzelne Blockelemente per Parameter definieren
$scriptProperties['custom']['showCategories'] = isset($showCategories) ? $showCategories : '1';
$scriptProperties['custom']['showMinical'] = isset($showMinical) ? $showMinical : '1';
$scriptProperties['custom']['showQuicklinks'] = isset($showQuicklinks) ? $showQuicklinks : '1';
$scriptProperties['custom']['showEventlist'] = isset($showEventlist) ? $showEventlist : '1';

$modx->regClientCSS('assets/components/migxcalendars/css/minical.css');
$modx->regClientCSS('assets/components/migxcalendars/js/jbox/jBox.css');
$modx->regClientStartupScript('http://code.jquery.com/jquery-1.11.1.min.js');
$modx->regClientStartupScript('assets/components/migxcalendars/js/jbox/jBox.min.js');
$modx->regClientStartupScript('assets/components/migxcalendars/js/jbox/init.js');

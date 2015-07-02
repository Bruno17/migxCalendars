<?php

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
$scriptProperties['task'] = 'availiabledates'; //Taskverzeichnis core/components/{component}/bloxprojects/{project}/{task}/

$modx->regClientCSS('assets/components/migxcalendars/css/minical.css');

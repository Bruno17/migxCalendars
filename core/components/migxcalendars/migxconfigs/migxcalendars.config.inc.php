<?php

$action = $this->modx->getOption('action', $_REQUEST, '');
$langloaded = $this->modx->getOption('langloaded', $_REQUEST, false);

if (!$langloaded) {
    $_REQUEST['langloaded'] = true;
    $this->modx->lexicon->load('migxcalendars:default');
    $this->loadLang('migxcal');
}
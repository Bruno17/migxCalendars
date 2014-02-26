<?php

$action = $this->modx->getOption('processaction', $_REQUEST, '');

switch ($action) {
    case 'emptythrash':
        $this->customconfigs['classname'] = 'migxCalendarEvents';
        break;
}

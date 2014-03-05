<?php

$config = $modx->migx->customconfigs;
$options = array();
$options[] = array('combo_name' => $modx->lexicon('migxcal.active_filter'), 'combo_id' => 'all');
$options[] = array('combo_name' => $modx->lexicon('migxcal.active'), 'combo_id' => '1');
$options[] = array('combo_name' => $modx->lexicon('migxcal.inactive'), 'combo_id' => '0');


$count = count($options);
return $this->outputArray($options, $count);
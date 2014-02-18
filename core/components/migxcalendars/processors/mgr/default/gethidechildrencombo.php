<?php

$config = $modx->migx->customconfigs;
$options = array();
$options[] = array('combo_name' => 'Alle zeigen', 'combo_id' => '9999999999');
$options[] = array('combo_name' => 'Wiederholungen verbergen', 'combo_id' => '1');

$count = count($options);
return $this->outputArray($options, $count);
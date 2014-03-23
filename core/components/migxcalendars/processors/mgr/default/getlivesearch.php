<?php

$config = $modx->migx->customconfigs;

$prefix = isset($config['prefix']) && !empty($config['prefix']) ? $config['prefix'] : null;
if (isset($config['use_custom_prefix']) && !empty($config['use_custom_prefix'])) {
    $prefix = isset($config['prefix']) ? $config['prefix'] : '';
}

if (!empty($config['packageName'])) {
    $packageNames = explode(',', $config['packageName']);
    //all packages must have the same prefix for now!
    foreach ($packageNames as $packageName) {
        $packagepath = $modx->getOption('core_path') . 'components/' . $packageName . '/';
        $modelpath = $packagepath . 'model/';

        $modx->addPackage($packageName, $modelpath, $prefix);
    }
}

$classname = $config['classname'];
$joins = isset($config['joins']) && !empty($config['joins']) ? $modx->fromJson($config['joins']) : false;

$isLimit = !empty($scriptProperties['limit']);
$isCombo = !empty($scriptProperties['combo']);
$start = $modx->getOption('start', $scriptProperties, 0);
$limit = $modx->getOption('limit', $scriptProperties, 10);
$query = $modx->getOption('query', $scriptProperties, '');
$sort = 'id';
$dir = 'DESC';


$c = $modx->newQuery($classname);
$c->select($modx->getSelectColumns($classname, $classname));
if ($joins) {
    $modx->migx->prepareJoins($classname, $joins, $c);
}
//$c->where(array('status:!=' => 'closed'));

if (!empty($query)){
$c->where(array(
    'title:LIKE' => '%' . $query . '%',
    'OR:description:LIKE' => '%' . $query . '%',
    'OR:id:=' => $query));    
}
    
$count = $modx->getCount($classname, $c);

$c->limit($limit, $start);

$c->sortby($sort, $dir);
$stmt = $c->prepare();
//echo $c->toSql();
$stmt->execute();
$result = $stmt->fetchAll();

$rows = array();
foreach ($result as $row) {
    $row['fullname'] = !empty($row['UserProfile_fullname']) ? $row['UserProfile_fullname'] : $row['fullname'];
    $row['city'] = !empty($row['UserProfile_city']) ? $row['UserProfile_city'] : $row['city'];
    $row['address'] = !empty($row['UserProfile_address']) ? $row['UserProfile_address'] : $row['address'];
    $row['value'] = $row['id'];
    $row['createdon_formated'] = strftime('%d.%m.%Y %H:%M', strtotime($row['createdon']));
    $row['text'] = $row['id'] . ' - ' . $row['fullname'];
    $row['display'] = $modx->getChunk('booking_livesearchTpl', $row);
    $rows[] = $row;
}


return $this->outputArray($rows, $count);
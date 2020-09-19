<?php
/* @var array $scriptProperties */
/* @var LMS $LMS */
$LMS = $modx->getService('lms', 'LMS', $modx->getOption('lms.core_path', null, $modx->getOption('core_path') . 'components/lms/') . 'model/lms/', $scriptProperties);
$LMS->initialize($modx->context->key, $scriptProperties);

/** @var pdoFetch $pdoFetch */
$fqn = $modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
if ($pdoClass = $modx->loadClass($fqn, '', false, true)) {
	$pdoFetch = new $pdoClass($modx, $scriptProperties);
}
elseif ($pdoClass = $modx->loadClass($fqn, MODX_CORE_PATH . 'components/pdotools/model/', false, true)) {
	$pdoFetch = new $pdoClass($modx, $scriptProperties);
}
else {
	$modx->log(modX::LOG_LEVEL_ERROR, 'Could not load pdoFetch from "MODX_CORE_PATH/components/pdotools/model/".');
	return false;
}
$pdoFetch->addTime('pdoTools loaded');

if (isset($parents) && $parents === '') {
	$scriptProperties['parents'] = $modx->resource->id;
}

//Filter by user
$is_manager = false;
// If role is 2 (Super User) in primary group
if ($LMS->primaryGroup->get('role') == 2) {
	$primary_group = $modx->user->getPrimaryGroup();
	$members = $primary_group->getUsersIn();

	foreach($members as $member) {
		$ids[] = $member->get('id');
	}

	$where['user_id:IN'] = $ids;
	$where['id:!='] = $modx->user->id;
	$is_manager = true;
}
else {
	$where['user_id'] = $modx->user->id;
}

$class = "Statistic";

// Joining tables

$leftJoin = array(
	'User' => array('class' => 'modUser', 'on' => '`User`.`id` = `Statistic`.`user_id`'),
	'Profile' => array('class' => 'modUserProfile', 'on' => '`Profile`.`internalKey` = `Statistic`.`user_id`'),
);

$innerJoin = array(
	'Test' => array('class' => 'Test', 'on' => '`Test`.`id` = `Statistic`.`parent` AND `Test`.`class_key`="Test"'),
);

// Fields to select
$select = array(
	'Test' => $modx->getSelectColumns('Test', 'Test', 'test.', array('pagetitle')),
	'User' => $modx->getSelectColumns('modUser', 'User', 'user.', array('username')),
	'Profile' => $modx->getSelectColumns('modUserProfile', 'Profile', 'user.', array('fullname')),
	'Statistic' => $modx->getSelectColumns($class, $class)
);

$pdoFetch->addTime('Conditions prepared');

// Add custom parameters
foreach (array('where', 'select', 'leftJoin') as $v) {
	if (!empty($scriptProperties[$v])) {
		$tmp = $modx->fromJSON($scriptProperties[$v]);
		if (is_array($tmp)) {
			$$v = array_merge($$v, $tmp);
		}
	}
	unset($scriptProperties[$v]);
}


$default = array(
	'class' => $class,
	'where' => $modx->toJSON($where),
	'leftJoin' => $modx->toJSON($leftJoin),
	'innerJoin' => $modx->toJSON($innerJoin),
	'select' => $modx->toJSON($select),
	'sortby' => 'editedon',
	'sortdir' => 'DESC',
	'groupby' => $class . '.id',
	'return' => !empty($returnIds)
		? 'ids'
		: 'data',
	'nestedChunkPrefix' => 'statistic_',
);

// Merge all properties and run!
$pdoFetch->setConfig(array_merge($default, $scriptProperties));
$pdoFetch->addTime('Query parameters are prepared.');
$rows = $pdoFetch->run();

if (!empty($returnIds)) {
	return $rows;
}

// Processing rows
$output = array();
if (!empty($rows) && is_array($rows)) {
	foreach ($rows as $k => $row) {

		$row['is_manager'] = $is_manager;
		$row['class'] = empty($row['finished']) ? 'red' : 'green';

		// Processing chunk
		$tpl = $pdoFetch->defineChunk($row);
		$output[] = empty($tpl)
			? '<pre>' . $pdoFetch->getChunk('', $row) . '</pre>'
			: $pdoFetch->getChunk($tpl, $row, $pdoFetch->config['fastMode']);
	}
}
$pdoFetch->addTime('Returning processed chunks');

$log = '';
if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
	$log .= '<pre class="getLMSLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

// Return output
if (!empty($toSeparatePlaceholders)) {
	$output['log'] = $log;
	$modx->setPlaceholders($output, $toSeparatePlaceholders);
}
else {
	if (empty($outputSeparator)) {
		$outputSeparator = "\n";
	}
	$output = implode($outputSeparator, $output);
	$output .= $log;

	if (!empty($tplWrapper) && (!empty($wrapIfEmpty) || !empty($output))) {
		$array = array('output' => $output, 'is_manager' => $is_manager);
		$output = $pdoFetch->getChunk($tplWrapper, $array, $pdoFetch->config['fastMode']);
	}

	if (!empty($toPlaceholder)) {
		$modx->setPlaceholder($toPlaceholder, $output);
	}
	else {
		return $output;
	}
}
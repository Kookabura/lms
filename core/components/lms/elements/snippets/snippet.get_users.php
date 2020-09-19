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
if ($LMS->primaryGroup) {
	$members = $LMS->primaryGroup->getUsersIn();

	foreach($members as $member) {
		$ids[] = $member->get('id');
	}

	$where['id:IN'] = $ids;
	$where['id:!='] = $modx->user->id;
}

$class = 'modUser';

// Joining tables
$leftJoin = array(
	'Member' => array('class' => 'modUserGroupMember', 'on' => '`Member`.`member` = `modUser`.`id` AND `Member`.`user_group` = ' . $LMS->primaryGroup->get('id')),
	'Role' => array('class' => 'modUserGroupRole', 'on' => '`Role`.`id` = `Member`.`role`'),
	'Profile' => array('class' => 'modUserProfile', 'on' => '`Profile`.`internalKey` = `modUser`.`id`'),
);

// Fields to select
$select = array(
	'Role' => $modx->getSelectColumns('modUserGroupRole', 'Role', 'role.', array('id', 'name')),
	'Profile' => $modx->getSelectColumns('modUserProfile', 'Profile', 'user.', array('fullname', 'email')),
	'modUser' => $modx->getSelectColumns($class, $class)
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
	'select' => $modx->toJSON($select),
	'sortby' => 'id',
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
		$array = array('output' => $output);
		$output = $pdoFetch->getChunk($tplWrapper, $array, $pdoFetch->config['fastMode']);
	}

	if (!empty($toPlaceholder)) {
		$modx->setPlaceholder($toPlaceholder, $output);
	}
	else {
		return $output;
	}
}
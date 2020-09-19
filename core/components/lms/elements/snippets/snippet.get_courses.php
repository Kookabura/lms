<?php
/* @var array $scriptProperties */
/* @var LMS $LMS */
$LMS = $modx->getService('lms', 'LMS', $modx->getOption('lms.core_path', null, $modx->getOption('core_path') . 'components/lms/') . 'model/lms/', $scriptProperties);
$LMS->initialize($modx->context->key, $scriptProperties);
if (!$LMS->primaryGroup) return false;

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

$class = 'Course';

$where = array(
	'class_key' => $class,
);

if ($LMS->primaryGroup->get('role') != 2) {
	$where[] = 'FIND_IN_SET('.$LMS->primaryGroup->get('role').', REPLACE(roles, "||", ",")) > 0';
}

// Adding custom where parameters
if (!empty($scriptProperties['where'])) {
	$tmp = $modx->fromJSON($scriptProperties['where']);
	if (is_array($tmp)) {
		$where = array_merge($where, $tmp);
	}
}

unset($scriptProperties['where']);
$pdoFetch->addTime('"Where" expression built.');

// Joining tables
$leftJoin = array(
	'Module' => array('class' => 'Module', 'on' => 'Module.parent=Course.id AND Module.published=1 AND Module.deleted=0 AND Module.class_key="Module"'),
	'Test' => array('class' => 'Test', 'on' => 'Test.parent=Course.id AND Test.published=1 AND Test.deleted=0 AND Test.class_key="Test"'),
	'Statistic' => array('class' => 'Statistic', 'on' => 'Statistic.parent=Course.id AND Statistic.user_id=' . $modx->user->get('id')),
);

// Fields to select
$select = array(
	'Course' => !empty($includeContent)
		? $modx->getSelectColumns($class, $class)
		: $modx->getSelectColumns($class, $class, '', array('content'), true),
	'Module' => 'COUNT(DISTINCT `Module`.`id`) as `lms`',
	'Statistic' => $modx->getSelectColumns('Statistic', 'Statistic', '', array('progress', 'finished')),
);

$default = array(
	'class' => $class,
	'where' => $modx->toJSON($where),
	'leftJoin' => $modx->toJSON($leftJoin),
	'select' => $modx->toJSON($select),
	'groupby' => $class . '.id',
	'sortby' => 'pagetitle',
	'sortdir' => 'DESC',
	'return' => !empty($returnIds)
		? 'ids'
		: 'data',
	'nestedChunkPrefix' => 'lms_',
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
		// Processing main fields

		$row['date_ago'] = $LMS->dateFormat($row['createdon']);
		$row['class'] = isset($row['finished']) ? ($row['finished'] == 0 ? 'class="ref"' : 'class="yes"') : null ;

		$row['idx'] = $pdoFetch->idx++;
		// Processing chunk
		$tpl = $pdoFetch->defineChunk($row);
		$output[] = empty($tpl)
			? '<pre>' . $pdoFetch->getChunk('', $row) . '</pre>'
			: $pdoFetch->getChunk($tpl, $row, $pdoFetch->config['fastMode']);
	}
}
$pdoFetch->addTime('Returning processed chunks');
if (empty($outputSeparator)) {
	$outputSeparator = "\n";
}
$output = implode($outputSeparator, $output);

$log = '';
if ($modx->user->hasSessionContext('mgr') && !empty($showLog)) {
	$log .= '<pre class="getSectionsLog">' . print_r($pdoFetch->getTime(), 1) . '</pre>';
}

// Return output
if (!empty($toSeparatePlaceholders)) {
	$output['log'] = $log;
	$modx->setPlaceholders($output, $toSeparatePlaceholders);
}
else {
	$output .= $log;

	if (!empty($tplWrapper) && (!empty($wrapIfEmpty) || !empty($output))) {
		$output = $pdoFetch->getChunk($tplWrapper, array('output' => $output), $pdoFetch->config['fastMode']);
	}

	if (!empty($toPlaceholder)) {
		$modx->setPlaceholder($toPlaceholder, $output);
	}
	else {
		return $output;
	}
}
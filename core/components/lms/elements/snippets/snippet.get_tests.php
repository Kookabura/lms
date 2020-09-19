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

$class = 'Test';
$where = array('class_key' => $class);

//Filter by user
if (!empty($user)) {
	$user = array_map('trim', explode(',', $user));
	$user_id = $user_username = array();
	foreach ($user as $v) {
		if (is_numeric($v)) {
			$user_id[] = $v;
		}
		else {
			$user_username[] = $v;
		}
	}
	if (!empty($user_id) && !empty($user_username)) {
		$where[] = '(`User`.`id` IN (' . implode(',', $user_id) . ') OR `User`.`username` IN (\'' . implode('\',\'', $user_username) . '\'))';
	}
	else {
		if (!empty($user_id)) {
			$where['User.id:IN'] = $user_id;
		}
		else {
			if (!empty($user_username)) {
				$where['User.username:IN'] = $user_username;
			}
		}
	}
}

// Joining tables
$leftJoin = array(
	'Course' => array('class' => 'Course', 'on' => '`Course`.`id` = `Test`.`parent`'),
	'Statistic' => array('class' => 'Statistic', 'on' => '`Statistic`.`parent` = `Test`.`id` AND Statistic.user_id = '. $modx->user->get('id')),
);

// Fields to select
$select = array(
	'Course' => $modx->getSelectColumns('Course', 'Course', 'section.', array('content'), true),
	'Statistic' => $modx->getSelectColumns('Statistic', 'Statistic', '', array('progress', 'finished')),
	'Test' => $modx->getSelectColumns($class, $class, '', array('content'), true),
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
	'sortby' => 'createdon',
	'sortdir' => 'DESC',
	'groupby' => $class . '.id',
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
		// Handle properties
		$properties = is_string($row['properties'])
			? $modx->fromJSON($row['properties'])
			: $row['properties'];
		if (!empty($properties['lms'])) {
			$properties = $properties['lms'];
		}

		if (!is_array($properties)) {
			$properties = array();
		}

		$row['active'] = $LMS->isTestReady($row['parent']) 
			? ($row['finished'] ? null : 'active')
			: null;

		$row['class'] = empty($row['finished']) 
			? 'class="'.$active.'"'
			: 'class="yes2 '. $active. '"';

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
		if ($LMS->authenticated && $modx->resource->class_key == 'Course') {
			/** @var Course $section */
			$section = &$modx->resource;
		}
		$output = $pdoFetch->getChunk($tplWrapper, $array, $pdoFetch->config['fastMode']);
	}

	if (!empty($toPlaceholder)) {
		$modx->setPlaceholder($toPlaceholder, $output);
	}
	else {
		return $output;
	}
}
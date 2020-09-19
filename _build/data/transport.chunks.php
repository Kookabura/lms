<?php
/**
 * Add chunks to build
 */

$chunks = array();

$tmp = array(
	'tpl.LMS.player' => 'player',
	'tpl.LMS.statistic.row' => 'statistic_row',
	'tpl.LMS.users.row' => 'users_row',
	'tpl.LMS.test.row' => 'test_row',
	'tpl.LMS.module.row' => 'module_row',
	'tpl.LMS.course.row' => 'course_row',
	'tpl.LMS.user.create' => 'user_create',
	'tpl.LMS.professions.row' => 'professions_row',
	'tpl.LMS.user.get.outer' => 'user_get_outer',
	'tpl.LMS.statistic.outer' => 'statistic_outer',
	'tpl.LMS.student.act.email' => 'student_act_email',
	'tpl.LMS.manager.act.email' => 'manager_act_email',
	'tpl.LMS.new.student.email' => 'new_student_email'
);

// Save chunks for setup options
$BUILD_CHUNKS = array();

foreach ($tmp as $k => $v) {
	/* @avr modChunk $chunk */
	$chunk = $modx->newObject('modChunk');
	$chunk->fromArray(array(
		'name' => $k,
		'description' => '',
		'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.'.$v.'.tpl'),
		'static' => BUILD_CHUNK_STATIC,
		'source' => 1,
		'static_file' => 'core/components/'.PKG_NAME_LOWER.'/elements/chunks/chunk.'.$v.'.tpl',
		),'',true,true);

	$chunks[] = $chunk;

	$BUILD_CHUNKS[$k] = file_get_contents($sources['source_core'].'/elements/chunks/chunk.'.$v.'.tpl');
}

ksort($BUILD_CHUNKS);
return $chunks;
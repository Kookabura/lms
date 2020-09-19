<?php

$properties = array();

$tmp = array(
	'tpl' => array(
		'type' => 'textfield',
		'value' => 'tpl.LMS.statistic.row',
	),
	'limit' => array(
		'type' => 'numberfield',
		'value' => 10,
	),
	'offset' => array(
		'type' => 'numberfield',
		'value' => 0,
	),
	'depth' => array(
		'type' => 'numberfield',
		'value' => 10,
	),
	'parents' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'resources' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'sortby' => array(
		'type' => 'textfield',
		'value' => 'editedon',
	),
	'sortdir' => array(
		'type' => 'list',
		'options' => array(
			array('text' => 'ASC','value' => 'ASC'),
			array('text' => 'DESC','value' => 'DESC'),
		),
		'value' => 'DESC',
	),
	'toPlaceholder' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'where' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'outputSeparator' => array(
		'type' => 'textfield',
		'value' => "\n",
	),
	'showLog' => array(
		'type' => 'combo-boolean',
		'value' => false,
	),
	'fastMode' => array(
		'type' => 'combo-boolean',
		'value' => false,
	),
	'user' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'tplWrapper' => array(
		'type' => 'textfield',
		'value' => 'tpl.LMS.statistic.outer',
	),
	'wrapIfEmpty' => array(
		'type' => 'combo-boolean',
		'value' => 'true',
	),
);

foreach ($tmp as $k => $v) {
	$properties[$k] = array_merge(array(
		'name' => $k,
		'desc' => 'lms_prop_'.$k,
		'lexicon' => 'lms:properties',
	), $v);
}

return $properties;
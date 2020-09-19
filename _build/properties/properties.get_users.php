<?php

$properties = array();

$tmp = array(
	'tpl' => array(
		'type' => 'textfield',
		'value' => 'tpl.LMS.users.row',
	),
	'limit' => array(
		'type' => 'numberfield',
		'value' => 10,
	),
	'offset' => array(
		'type' => 'numberfield',
		'value' => 0,
	),
	'resources' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'sortby' => array(
		'type' => 'textfield',
		'value' => 'id',
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
	'tplWrapper' => array(
		'type' => 'textfield',
		'value' => 'tpl.LMS.user.get.outer',
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
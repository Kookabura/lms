<?php
$properties = array();
$tmp = array(
	'tplUserRow' => array(
		'type' => 'textfield',
		'value' => 'tpl.LMS.users.row',
	),
	'tplFormCreate' => array(
		'type' => 'textfield',
		'value' => 'tpl.LMS.user.create',
	),
	'tplProfessionRow' => array(
		'type' => 'textfield',
		'value' => '@INLINE <option value="[[+id]]" [[+selected]]>[[+name]]</option>',
	),
	'allowedFields' => array(
		'type' => 'textfield',
		'value' => 'username,fullname,active,role',
	),
	'bypassFields' => array(
		'type' => 'textfield',
		'value' => '',
	),
	'sortby' => array(
		'type' => 'textfield',
		'value' => 'name',
		'desc' => 'lms_prop_professions_sortby'
	),
	'sortdir' => array(
		'type' => 'list',
		'options' => array(
			array('text' => 'ASC','value' => 'ASC'),
			array('text' => 'DESC','value' => 'DESC'),
		),
		'value' => 'ASC',
		'desc' => 'lms_prop_professions_sortdir'
	),
	'allowFiles' => array(
		'type' => 'combo-boolean',
		'value' => true,
	),
	'source' => array(
		'type' => 'numberfield',
		'value' => 0,
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
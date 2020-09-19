<?php
/**
 * Add snippets to build
 */
$snippets = array();

$tmp = array(
	'getModules' => 'get_modules',
	'getTests' => 'get_tests',
	'getCourses' => 'get_courses',
	'getStatistic' => 'get_statistic',
	'getUsers' => 'get_users',
	'UserForm' => 'user_form',
	'getCompanyImage' => 'get_image'
);

foreach ($tmp as $k => $v) {
	/* @avr modSnippet $snippet */
	$snippet = $modx->newObject('modSnippet');
	$snippet->fromArray(array(
		'name' => $k,
		'description' => '',
		'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.'.$v.'.php'),
		'static' => BUILD_SNIPPET_STATIC,
		'source' => 1,
		'static_file' => 'core/components/'.PKG_NAME_LOWER.'/elements/snippets/snippet.'.$v.'.php',
	),'',true,true);

	$properties = include $sources['build'].'properties/properties.'.$v.'.php';
	$snippet->setProperties($properties);

	$snippets[] = $snippet;
}

unset($properties);
return $snippets;
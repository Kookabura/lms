<?php
/* @var array $scriptProperties */
/* @var LMS $LMS */
$LMS = $modx->getService('lms', 'LMS', $modx->getOption('lms.core_path', null, $modx->getOption('core_path') . 'components/lms/') . 'model/lms/', $scriptProperties);
$LMS->initialize($modx->context->key, $scriptProperties);

if (!$LMS->config['profession_role_num']) {
	return $modx->lexicon('prof_err_empty');
}
$tplProfessionRow = $modx->getOption('tplProfessionRow', $scriptProperties, 'tpl.LMS.professions.row');
$tplFormCreate = $modx->getOption('tplFormCreate', $scriptProperties, 'tpl.LMS.user.create');

$data = array();

$tplWrapper = $tplFormCreate;

// Get available professions for ticket create
$data['professions'] = '';
/** @var modProcessorResponse $response */
$response = $LMS->runProcessor('web/profession/getlist', array(
	'authority' => $LMS->config['profession_role_num'],
	'sortby' => !empty($scriptProperties['sortby'])
		? $scriptProperties['sortby']
		: 'name',
	'sortdir' => !empty($scriptProperties['sortdir'])
		? $scriptProperties['sortdir']
		: 'asc',
	'context' => !empty($scriptProperties['context'])
		? $scriptProperties['context']
		: $modx->context->key,
	'limit' => 0,
));
if ($response->isError()) {
	return $response->getMessage();
}
else {
	$response = $modx->fromJSON($response->getResponse());
}
if (!empty($response['results'])) {
	$LMS->config['professions'] = array();
	foreach ($response['results'] as $v) {
		$v['selected'] = $parent == $v['id'] || $parent == $v['alias']
			? 'selected'
			: '';
		$data['professions'] .= $LMS->getChunk($tplProfessionRow, $v);
		$LMS->config['professions'][] = $v['id'];
	}
}

$output = $LMS->getChunk($tplWrapper, $data);
$_SESSION['LMS'] = $LMS->config;
return $output;
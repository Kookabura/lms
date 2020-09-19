<?php
/* @var array $scriptProperties */
/* @var LMS $LMS */
$LMS = $modx->getService('lms', 'LMS', $modx->getOption('lms.core_path', null, $modx->getOption('core_path') . 'components/lms/') . 'model/lms/', $scriptProperties);
$LMS->initialize($modx->context->key, $scriptProperties);
if (!$LMS->primaryGroup) return false;

$type = isset($type) ? $type : 'bg';
$postfix = empty($postfix) ? null : $postfix;
$hash = sha1($LMS->primaryGroup->get('name'));

$mediaSource = $modx->getObject('sources.modMediaSource', $modx->getOption('lms.source_default'));
$mediaSource->set('ctx', $this->modx->context->key);
if ($mediaSource->initialize()) {

	$bases = $mediaSource->getBases();
	
	$path = $bases['path'] . 'companies/' . $type . $hash . $postfix . '.jpg';
	
	if (file_exists(MODX_BASE_PATH . $path)) {
		if ($toPlaceholder) {
			$modx->setPlaceholder($toPlaceholder, $path);
		}
		else {
			return $path;
		}
	}
}

<?php

if (empty($_REQUEST['action'])) {
	die('Access denied');
}
else {
	$action = $_REQUEST['action'];
}

define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/index.php';

$modx->getService('error','error.modError');
$modx->getRequest();
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
$modx->error->message = null;

$properties = array();
$properties = $_SESSION['LMS'];

// Switch context
$context = 'web';
if ($context != 'web') {
	$modx->switchContext($context);
}

/* @var LMS $LMS */
define('MODX_ACTION_MODE', true);
$LMS = $modx->getService('lms','LMS',$modx->getOption('lms.core_path',null,$modx->getOption('core_path').'components/lms/').'model/lms/', $properties);
if ($modx->error->hasError() || !($LMS instanceof LMS)) {
	die('Error');
}

switch ($action) {
	case 'module/load': $response = $LMS->loadModule($_REQUEST['id']); break;
	case 'module/update_progress': $response = $LMS->updateProgress($_REQUEST['progress']); break;

	case 'test/process_results': $response = $LMS->updateProgress($_REQUEST['awarded'], $_REQUEST['passing']); break;

	case 'student/create': $response = $LMS->createStudent($_POST); break;
	case 'student/removemultiple':
	case 'student/activatemultiple':
	case 'student/deactivatemultiple': $response = $LMS->processStudent($_POST['id'], $action); break;

	case 'file/upload': $response = $LMS->fileUpload($_POST); break;
	case 'file/delete': $response = $LMS->fileDelete($_REQUEST['file']); break;

	default:
		$message = $_REQUEST['action'] != $action ? 'lms_err_register_globals' : 'lms_err_unknown';
		$response = $modx->toJSON(array('success' => false, 'message' => $modx->lexicon($message)));
}

if (is_array($response)) {
	$response = $modx->toJSON($response);
}

@session_write_close();
exit($response);
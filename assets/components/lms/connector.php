<?php
/**
 * LMS Connector
 *
 * @package lms
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('lms.core_path',null,$modx->getOption('core_path').'components/lms/');
require_once $corePath.'model/lms/lms.class.php';
$modx->LMS = new LMS($modx);

$modx->lexicon->load('lms:default');

/* handle request */
$path = $modx->getOption('processorsPath',$modx->LMS->config,$corePath.'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));
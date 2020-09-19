<?php

class LMSHomeManagerController extends modExtraManagerController {

	/**
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('lms:default');
	}


	/**
	 * @return null|string
	 */
	public function getPageTitle() {
		return $this->modx->lexicon('lms');
	}


	/**
	 *
	 */
	public function loadCustomCssJs() {
		/** @var LMS $LMS */
		$LMS = $this->modx->getService('LMS');

		$LMS->loadManagerFiles($this, array(
			'config' => true,
			'utils' => true,
			'css' => true,
			'modules' => true,
			'tests' => true,
			'statistic' => true,
			'company' => true,
			'profession' => true,
			'access' => true,
			'report' => true
		));
		$this->addLastJavascript($LMS->config['jsUrl'] . 'mgr/home.js');
		$this->addHtml('
		<script type="text/javascript">
		Ext.onReady(function() {
			MODx.load({xtype: "lms-page-home"});
		});
		</script>');
	}


	/**
	 * @return string
	 */
	public function getTemplateFile() {
		/** @var LMS $LMS */
		$LMS = $this->modx->getService('LMS');

		return $LMS->config['templatesPath'] . 'home.tpl';
	}

}
<?php

/**
 * The create manager controller for Test.
 *
 * @package lms
 */
class TestCreateManagerController extends ResourceCreateManagerController {
	/** @var Course $resource */
	public $parent;
	/** @var Test $resource */
	public $resource;


	/**
	 * Returns language topics
	 *
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('resource', 'lms:default');
	}


	/**
	 * Return the default template for this resource
	 *
	 * @return int
	 */
	public function getDefaultTemplate() {
		$properties = $this->parent->getProperties();

		return $properties['template'];
	}


	/**
	 * Register custom CSS/JS for the page
	 *
	 * @return void
	 */
	public function loadCustomCssJs() {
		$html = $this->head['html'];
		parent::loadCustomCssJs();
		$this->head['html'] = $html;

		if (is_null($this->resourceArray['properties'])) {
			$this->resourceArray['properties'] = array();
		}
		$this->resourceArray['properties']['lms'] = $this->parent->getProperties('lms');

		/** @var LMS $LMS */
		$LMS = $this->modx->getService('LMS');
		$LMS->loadManagerFiles($this, array(
			'config' => true,
			'utils' => true,
			'css' => true,
			'test' => true,
		));
		$this->addLastJavascript($LMS->config['jsUrl'] . 'mgr/test/create.js');

		$ready = array(
			'xtype' => 'lms-page-test-create',
			'record' => $this->resourceArray,
			'publish_document' => (int)$this->canPublish,
			'canSave' => (int)$this->canSave,
			'show_tvs' => (int)!empty($this->tvCounts),
			'mode' => 'create',
		);
		$this->addHtml('
		<script type="text/javascript">
		// <![CDATA[
		MODx.config.publish_document = ' . (int)$this->canPublish . ';
		MODx.config.default_template = ' . $this->modx->getOption('lms.default_template', null, $this->modx->getOption('default_template'), true) . ';
		MODx.onDocFormRender = "' . $this->onDocFormRender . '";
		MODx.ctx = "' . $this->ctx . '";
		Ext.onReady(function() {
			MODx.load(' . $this->modx->toJSON($ready) . ');
		});
		// ]]>
		</script>');
	}

}

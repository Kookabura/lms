<?php

/**
 * The create manager controller for Course.
 *
 * @package lms
 */
class CourseCreateManagerController extends ResourceCreateManagerController {
	/** @var  Course $resource */
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
		$this->resourceArray['properties']['lms'] = $this->resource->getProperties('lms');
		$this->resourceArray['properties']['ratings'] = $this->resource->getProperties('ratings');
		$this->resourceArray['syncsite'] = 0;

		/** @var LMS $LMS */
		$LMS = $this->modx->getService('LMS');
		$LMS->loadManagerFiles($this, array(
			'config' => true,
			'utils' => true,
			'css' => true,
			'course' => true,
		));
		$this->addLastJavascript($LMS->config['jsUrl'] . 'mgr/course/create.js');

		$ready = array(
			'xtype' => 'lms-page-course-create',
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
		MODx.onDocFormRender = "' . $this->onDocFormRender . '";
		MODx.ctx = "' . $this->ctx . '";
		Ext.onReady(function() {
			MODx.load(' . $this->modx->toJSON($ready) . ');
		});
		// ]]>
		</script>');
	}

}

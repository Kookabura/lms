<?php

class ModuleUpdateManagerController extends ResourceUpdateManagerController {
	/** @var  Module $resource */
	public $resource;


	/**
	 * Returns language topics
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('resource', 'lms:default');
	}


	/**
	 * Check for any permissions or requirements to load page
	 * @return bool
	 */
	public function checkPermissions() {
		return $this->modx->hasPermission('new_document');
	}

	/**
	 * Register custom CSS/JS for the page
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

		/** @var LMS $LMS */
		$LMS = $this->modx->getService('LMS');
		$LMS->loadManagerFiles($this, array(
			'config' => true,
			'utils' => true,
			'css' => true,
			'module' => true,
			//'comments' => true,
		));
		$this->addLastJavascript($LMS->config['jsUrl'] . 'mgr/module/update.js');

		$ready = array(
			'xtype' => 'lms-page-module-update',
			'resource' => $this->resource->get('id'),
			'record' => $this->resourceArray,
			'publish_document' => (int)$this->canPublish,
			'preview_url' => $this->previewUrl,
			'locked' => (int)$this->locked,
			'lockedText' => $this->lockedText,
			'canSave' => (int)$this->canSave,
			'canEdit' => (int)$this->canEdit,
			'canCreate' => (int)$this->canCreate,
			'canDuplicate' => (int)$this->canDuplicate,
			'canDelete' => (int)$this->canDelete,
			'show_tvs' => (int)!empty($this->tvCounts),
			'mode' => 'update',
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

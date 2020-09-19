<?php

class LMS {
	/* @var modX $modx */
	public $modx;
	/* @var pdoTools $pdoTools */
	public $pdoTools;
	public $initialized = array();
	public $authenticated = false;
	private $prepareCommentCustom = null;
	private $last_view = 0;


	/**
	 * @param modX $modx
	 * @param array $config
	 */
	function __construct(modX &$modx, array $config = array()) {
		$this->modx =& $modx;

		$corePath = $this->modx->getOption('lms.core_path', $config, $this->modx->getOption('core_path') . 'components/lms/');
		$assetsPath = $this->modx->getOption('lms.assets_path', $config, $this->modx->getOption('assets_path') . 'components/lms/');
		$assetsUrl = $this->modx->getOption('lms.assets_url', $config, $this->modx->getOption('assets_url') . 'components/lms/');
		$actionUrl = $this->modx->getOption('lms.action_url', $config, $assetsUrl . 'action.php');
		$connectorUrl = $assetsUrl . 'connector.php';

		$this->config = array_merge(array(
			'assetsUrl' => $assetsUrl,
			'cssUrl' => $assetsUrl . 'css/',
			'jsUrl' => $assetsUrl . 'js/',
			'jsPath' => $assetsPath . 'js/',
			'imagesUrl' => $assetsUrl . 'img/',

			'connectorUrl' => $connectorUrl,
			'actionUrl' => $actionUrl,

			'corePath' => $corePath,
			'modelPath' => $corePath . 'model/',
			'chunksPath' => $corePath . 'elements/chunks/',
			'templatesPath' => $corePath . 'elements/templates/',
			'chunkSuffix' => '.chunk.tpl',
			'snippetsPath' => $corePath . 'elements/snippets/',
			'processorsPath' => $corePath . 'processors/',

			'fastMode' => false,
			'dateFormat' => 'd F Y, H:i',
			'dateNow' => 10,
			'dateDay' => 'day H:i',
			'dateMinutes' => 59,
			'dateHours' => 10,
			'charset' => $this->modx->getOption('modx_charset'),
			'snippetPrepareComment' => $this->modx->getOption('lms.snippet_prepare_comment'),
			'commentEditTime' => $this->modx->getOption('lms.comment_edit_time', null, 180),
			'depth' => 0,

			'gravatarUrl' => 'https://www.gravatar.com/avatar/',
			'gravatarSize' => 24,
			'gravatarIcon' => 'mm',

			'json_response' => true,
			'nestedChunkPrefix' => 'lms_',
			'allowGuest' => false,
			'allowGuestEdit' => false,
			'allowGuestEmails' => false,
			'enableCaptcha' => false,

			'requiredFields' => '',
			'managers_group' => $this->modx->getOption('lms.managers_group'),
			'profession_role_num' => $this->modx->getOption('lms.profession_role_num')
		), $config);

		$this->modx->addPackage('lms', $this->config['modelPath']);
		$this->modx->lexicon->load('lms:default');

		$this->authenticated = $this->modx->user->isAuthenticated($this->modx->context->get('key'));
		$this->primaryGroup = $this->getPrimaryGroup();
		/*$managerGroup = $this->modx->getObject('modUserGroup', $this->config['managers_group']);
		$this->managerGroup = $managerGroup->get('name');*/
	}


	/**
	 * Initializes component into different contexts.
	 *
	 * @param string $ctx The context to load. Defaults to web.
	 * @param array $scriptProperties
	 *
	 * @return boolean
	 */
	public function initialize($ctx = 'web', $scriptProperties = array()) {
		$this->config = array_merge($this->config, $scriptProperties);
		if (!$this->pdoTools) {
			$this->loadPdoTools();
		}
		$this->pdoTools->setConfig($this->config);

		$this->config['ctx'] = $ctx;
		if (empty($this->initialized[$ctx])) {
			$config_js = array(
				'ctx' => $ctx,
				'jsUrl' => $this->config['jsUrl'] . 'web/',
				'cssUrl' => $this->config['cssUrl'] . 'web/',
				'actionUrl' => $this->config['actionUrl'],
				'close_all_message' => $this->modx->lexicon('lms_message_close_all'),
				'tpanel' => (int)$this->authenticated,
				'enable_editor' => (int)$this->modx->getOption('lms.enable_editor'),
			);
			$this->modx->regClientStartupScript('<script type="text/javascript">LMSConfig=' . $this->modx->toJSON($config_js) . '</script>', true);
			$this->initialized[$ctx] = true;
		}

		if (!defined('MODX_API_MODE') || !MODX_API_MODE) {
			$config = $this->makePlaceholders($this->config);

			$css = !empty($this->config['frontend_css'])
				? $this->config['frontend_css']
				: $this->modx->getOption('lms.frontend_css');
			if (!empty($css) && preg_match('/\.css/i', $css)) {
				$this->modx->regClientCSS(str_replace($config['pl'], $config['vl'], $css));
			}

			$js = !empty($this->config['frontend_js'])
				? $this->config['frontend_js']
				: $this->modx->getOption('lms.frontend_js');
			if (!empty($js) && preg_match('/\.js/i', $js)) {
				$this->modx->regClientScript(str_replace($config['pl'], $config['vl'], $js));
			}

			$this->modx->regClientStartupScript($this->config['jsUrl']. 'web/ispring.js');
		}

		return true;
	}


	/**
	 * Shorthand for the call of processor
	 *
	 * @access public
	 *
	 * @param string $action Path to processor
	 * @param array $data Data to be transmitted to the processor
	 *
	 * @return mixed The result of the processor
	 */
	public function runProcessor($action = '', $data = array()) {
		if (empty($action)) {
			return false;
		}
		$this->modx->error->reset();
		$processorsPath = !empty($this->config['processorsPath'])
			? $this->config['processorsPath']
			: MODX_CORE_PATH . 'components/lms/processors/';

		return $this->modx->runProcessor($action, $data, array('processors_path' => $processorsPath));
	}


	 /**
     * Return the Primary Group of User
     *
     * @return modUserGroup|null
     */
    public function getPrimaryGroup() {
    	$userGroup = null;
    	if ($user = $this->modx->user) {
	        $userGroup = $user->getOne('PrimaryGroup');
	        if ($userGroup) {
		        $userGroupMember = $this->modx->getObject('modUserGroupMember', array(
						'user_group' => $userGroup->get('id'),
						'member'=> $user->get('id')
					)
				);
				$userGroup->set('role', $userGroupMember->get('role'));
	    	}
	    }
        return $userGroup;
    }


	/**
	 * Returns sanitized load of Module
	 *
	 * @param int $id module resource id
	 *
	 * @return array
	 */
	public function loadModule($id) {
		$message = '';
		$q = $this->modx->newQuery('modResource');
		$q->where(array(
				'id' => $id,
			)
		);
		$q->andCondition(
			array(
				'class_key:=' => 'Module',
				'OR:class_key:=' => 'Test'
			)
		);

		if ($module = $this->modx->getObject('modResource', $q)) {
				
			$groups = $module->getResourceGroupNames();
			
			if ($this->modx->user->isMember($groups)) {

				$result = $this->getChunk($this->config['tplModuleAjax'], $module->toArray());
				$result = $this->pdoTools->fastProcess($result);
				$_SESSION['LMS']['module'] = $id;
				return $this->success($message, array('module' => $result));
			}
		} else {
			return $this->error($this->modx->lexicon('err_no_module'));
		}
	}

	/**
	* Check if user is allowed to pass the test
	*
	* @param int $course_id id of course
	*
	* @return boolean
	*/
	public function isTestReady($course_id) {
		$q = $this->modx->newQuery('Module');
		$q->select($this->modx->getSelectColumns('Module','Module','',array('id')));
		$q->where(array(
			'parent' => $course_id,
			'published' => true,
			'deleted' => false,
			'class_key' => 'Module'
			)
		);
		$modules = $this->modx->getIterator('Module',$q);
		foreach ($modules as $module) {
			$ids[] = $module->get('id');
		}
		
		$с = $this->modx->newQuery('Statistic');
		$с->where(array(
				'parent:IN' => $ids,
				'finished' => 1,
				'user_id' => $this->modx->user->id
			)
		);
		$statistic = $this->modx->getCount('Statistic', $с);

		$testIsReady = $statistic >= count($ids) ? true : false;

		return $testIsReady;
	}


	/**
	* Updates user progress on module, test, or course
	*
	* @param int $progress progress value
	* @param int $threshold threshold to pass test, module or course
	*
	* @return 
	*/
	public function updateProgress($progress, $threshold = 100) {
		$message = '';
		$module = $_SESSION['LMS']['module'];
		if (!empty($module)) {
			$stat = $this->modx->getObject('Statistic', array(
					'user_id' => $this->modx->user->id,
					'parent' => $module,
				)
			);
			if ($stat && $stat->get('progress') <= $progress) {
				$data = array(
					'id' => $stat->get('id')
					,'progress' => $progress
					,'editedon' => time()
					,'finished' => $progress >= $threshold ? 1 : 0
				);
				$response = $this->runProcessor('mgr/statistic/update', $data);
			}
			elseif (empty($stat)) {
				$data = array(
					'user_id' => $this->modx->user->id,
					'parent' => $module,
					'progress' => $progress,
					'finished' => $progress >= $threshold ? 1 : 0,
					'editedon' => time()
				);
				$response = $this->runProcessor('mgr/statistic/create', $data);
			}

			$result = array();			
			if ($response) {
				$message = $response->getMessage();
				$result = $response->getObject();

				// Handle stat for Course
				$module = $this->modx->getObject('modResource', array(
					'id' => $module,
					)
				);

				if ($module) {

					// Add tests to output if all modules are finished
					if ($progress == 100 && $module->get('class_key') == 'Module') {
						if ($this->isTestReady($module->get('parent'))) {
							$result['tests'] = $this->modx->runSnippet('getTests', array(
									'parents' => $module->get('parent')
								)
							);
						}
					}

				}
			}

			return $this->success($message, $result);
		} else {
			return $this->error($this->modx->lexicon('err_no_module'));
		}
	}

	/**
	* Create new student from fontend
	*
	* @param
	*
	*
	*/
	public function createStudent($data = array()) {
		$allowedFields = array_map('trim', explode(',', $this->config['allowedFields']));
		$bypassFields = array_map('trim', explode(',', $this->config['bypassFields']));

		$fields = array();
		foreach ($allowedFields as $field) {
			if (in_array($field, $allowedFields) && array_key_exists($field, $data)) {
				$value = $data[$field];
				if (!in_array($field, $bypassFields)) {
					$value = $this->sanitizeString($value);
				}
				$fields[$field] = $value;
			}
		}

		$groups = array(
			array(
				'usergroup' => $this->primaryGroup->get('id'),
				'role' => $data['role'],
				'authority' => $this->config['profession_role_num']
			)
		);
		$fields['primary_group'] = $this->primaryGroup->get('id');
		$fields['groups'] = $groups;
		$response = $this->runProcessor('web/student/create', $fields);
		if ($response->isError()) {
			return $this->error($response->getMessage(), $response->getFieldErrors());
		}
		else {
			$output = $this->getChunk($this->config['tplUserRow'], $response->getObject());
			return $this->success($this->modx->lexicon('student_added'), array('output' => $output));
		}
	}


	/**
	* Multiple students update from fontend (delete, activate, deactivate)
	*
	* @param array $ids users ids
	* @param string $action what to do
	*
	* @return array
	*/
	public function processStudent($ids = array(), $action) {
		$users = array();
		foreach ($ids as $id) {
			$user = $this->modx->getObject('modUser', $id);
			if ($user->isMember($this->primaryGroup->get('name'))) {
				$users[] = $id;
			}
		}
		
		$fields = array(
			'users' => implode(',', $users)
		);

		$response = $this->runProcessor('web/'.$action, $fields);
		if ($response->isError()) {
			return $this->error($response->getMessage());
		}
		else {
			$output = array(
				'users' => $users,
				'action' => $action
			);
			return $this->success($this->modx->lexicon('student_processed'), $output);
		}
	}

	
	/**
	 * Sanitize MODX tags
	 *
	 * @param string $string Any string with MODX tags
	 *
	 * @return string String with html entities
	 */
	public function sanitizeString($string = '') {
		if (is_array($string)) {
			foreach ($string as $key => $value) {
				$string[$key] = $this->sanitizeString($value);
			}
			return $string;
		}

		$string = htmlentities(trim($string), ENT_QUOTES, "UTF-8");
		$string = preg_replace('/^@.*\b/', '', $string);
		$arr1 = array('[', ']', '`');
		$arr2 = array('&#091;', '&#093;', '&#096;');

		return str_replace($arr1, $arr2, $string);
	}


	/**
	 * Method for transform array to placeholders
	 *
	 * @var array $array With keys and values
	 * @var string $prefix Prefix for array keys
	 *
	 * @return array $array Two nested arrays with placeholders and values
	 */
	public function makePlaceholders(array $array = array(), $prefix = '') {
		if (!$this->pdoTools) {
			$this->loadPdoTools();
		}

		return $this->pdoTools->makePlaceholders($array, $prefix);
	}


	/**
	 * Loads an instance of pdoTools
	 *
	 * @return boolean
	 */
	public function loadPdoTools() {
		if (!is_object($this->pdoTools) || !($this->pdoTools instanceof pdoTools)) {
			/** @var pdoFetch $pdoFetch */
			$fqn = $this->modx->getOption('pdoFetch.class', null, 'pdotools.pdofetch', true);
			if ($pdoClass = $this->modx->loadClass($fqn, '', false, true)) {
				$this->pdoTools = new $pdoClass($this->modx, $this->config);
			}
			elseif ($pdoClass = $this->modx->loadClass($fqn, MODX_CORE_PATH . 'components/pdotools/model/', false, true)) {
				$this->pdoTools = new $pdoClass($this->modx, $this->config);
			}
			else {
				$this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not load pdoFetch from "MODX_CORE_PATH/components/pdotools/model/".');
			}
		}
		return !empty($this->pdoTools) && $this->pdoTools instanceof pdoTools;
	}


	/**
	 * Process and return the output from a Chunk by name.
	 *
	 * @param string $name The name of the chunk.
	 * @param array $properties An associative array of properties to process the Chunk with, treated as placeholders within the scope of the Element.
	 * @param boolean $fastMode If false, all MODX tags in chunk will be processed.
	 *
	 * @return string The processed output of the Chunk.
	 */
	public function getChunk($name, array $properties = array(), $fastMode = false) {
		if (!$this->modx->parser) {
			$this->modx->getParser();
		}
		if (!$this->pdoTools) {
			$this->loadPdoTools();
		}

		return $this->pdoTools->getChunk($name, $properties, $fastMode);
	}


	/**
	 * Formats date to "10 minutes ago" or "Yesterday in 22:10"
	 * This algorithm taken from https://github.com/livestreet/livestreet/blob/7a6039b21c326acf03c956772325e1398801c5fe/engine/modules/viewer/plugs/function.date_format.php
	 *
	 * @param string $date Timestamp to format
	 * @param string $dateFormat
	 *
	 * @return string
	 */
	public function dateFormat($date, $dateFormat = null) {
		$date = preg_match('/^\d+$/', $date)
			? $date
			: strtotime($date);
		$dateFormat = !empty($dateFormat)
			? $dateFormat
			: $this->config['dateFormat'];
		$current = time();
		$delta = $current - $date;

		if ($this->config['dateNow']) {
			if ($delta < $this->config['dateNow']) {
				return $this->modx->lexicon('ticket_date_now');
			}
		}

		if ($this->config['dateMinutes']) {
			$minutes = round(($delta) / 60);
			if ($minutes < $this->config['dateMinutes']) {
				if ($minutes > 0) {
					return $this->declension($minutes, $this->modx->lexicon('ticket_date_minutes_back', array('minutes' => $minutes)));
				}
				else {
					return $this->modx->lexicon('ticket_date_minutes_back_less');
				}
			}
		}

		if ($this->config['dateHours']) {
			$hours = round(($delta) / 3600);
			if ($hours < $this->config['dateHours']) {
				if ($hours > 0) {
					return $this->declension($hours, $this->modx->lexicon('ticket_date_hours_back', array('hours' => $hours)));
				}
				else {
					return $this->modx->lexicon('ticket_date_hours_back_less');
				}
			}
		}

		if ($this->config['dateDay']) {
			switch (date('Y-m-d', $date)) {
				case date('Y-m-d'):
					$day = $this->modx->lexicon('ticket_date_today');
					break;
				case date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'))):
					$day = $this->modx->lexicon('ticket_date_yesterday');
					break;
				case date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') + 1, date('Y'))):
					$day = $this->modx->lexicon('ticket_date_tomorrow');
					break;
				default:
					$day = null;
			}
			if ($day) {
				$format = str_replace("day", preg_replace("#(\w{1})#", '\\\${1}', $day), $this->config['dateDay']);
				return date($format, $date);
			}
		}

		$m = date("n", $date);
		$month_arr = $this->modx->fromJSON($this->modx->lexicon('ticket_date_months'));
		$month = $month_arr[$m - 1];

		$format = preg_replace("~(?<!\\\\)F~U", preg_replace('~(\w{1})~u', '\\\${1}', $month), $dateFormat);

		return date($format, $date);
	}


	/**
	 * Declension of words
	 * This algorithm taken from https://github.com/livestreet/livestreet/blob/eca10c0186c8174b774a2125d8af3760e1c34825/engine/modules/viewer/plugs/modifier.declension.php
	 *
	 * @param int $count
	 * @param string $forms
	 * @param string $lang
	 *
	 * @return string
	 */
	public function declension($count, $forms, $lang = null) {
		if (empty($lang)) {
			$lang = $this->modx->getOption('cultureKey', null, 'en');
		}
		$forms = $this->modx->fromJSON($forms);

		if ($lang == 'ru') {
			$mod100 = $count % 100;
			switch ($count % 10) {
				case 1:
					if ($mod100 == 11) {
						$text = $forms[2];
					}
					else {
						$text = $forms[0];
					}
					break;
				case 2:
				case 3:
				case 4:
					if (($mod100 > 10) && ($mod100 < 20)) {
						$text = $forms[2];
					}
					else {
						$text = $forms[1];
					}
					break;
				case 5:
				case 6:
				case 7:
				case 8:
				case 9:
				case 0:
				default:
					$text = $forms[2];
			}
		}
		else {
			if ($count == 1) {
				$text = $forms[0];
			}
			else {
				$text = $forms[1];
			}
		}
		return $text;

	}


	/**
	 * Upload file for ticket
	 *
	 * @param $data
	 * @param string $class
	 *
	 * @return array|string
	 */
	public function fileUpload($data) {
		if (!$this->authenticated || empty($this->config['allowFiles'])) {
			return $this->error('access_denied');
		}

		$data['source'] = $this->modx->getOption('lms.source_default');
		$data['company'] = $this->primaryGroup->get('name');

		/** @var modProcessorResponse $response */
		$response = $this->runProcessor('web/file/upload', $data);
		if ($response->isError()) {
			return $this->error($response->getMessage());
		}
		$file = $response->getObject();

		return $this->success($this->modx->lexicon('file_uploaded_succesfully'), $file);
	}


	/**
	 * Delete or restore uploaded file
	 *
	 * @param string $file
	 *
	 * @return array|string
	 */
	public function fileDelete($file) {
		if (!$this->authenticated || empty($this->config['allowFiles'])) {
			return $this->error('access_denied');
		}
		/** @var modProcessorResponse $response */
		$response = $this->runProcessor('web/file/delete', array('file' => $file));
		if ($response->isError()) {
			return $this->error($response->getMessage());
		}

		return $this->success($response->getMessage());
	}


	/**
	 * This method returns an error of the cart
	 *
	 * @param string $message A lexicon key for error message
	 * @param array $data Additional data
	 * @param array $placeholders Array with placeholders for lexicon entry
	 *
	 * @return array|string $response
	 */
	public function error($message = '', $data = array(), $placeholders = array()) {
		$response = array(
			'success' => false,
			'message' => $this->modx->lexicon($message, $placeholders),
			'data' => $data,
		);

		return $this->config['json_response']
			? $this->modx->toJSON($response)
			: $response;
	}


	/**
	 * This method returns an success of the cart
	 *
	 * @param string $message
	 * @param array $data
	 * @param array $placeholders
	 *
	 * @return array|string
	 */
	public function success($message = '', $data = array(), $placeholders = array()) {
		$response = array(
			'success' => true,
			'message' => $this->modx->lexicon($message, $placeholders),
			'data' => $data,
		);

		return $this->config['json_response']
			? $this->modx->toJSON($response)
			: $response;
	}


	/**
	 * Compares MODX version
	 *
	 * @param string $version
	 * @param string $dir
	 *
	 * @return bool
	 */
	public function systemVersion($version = '2.3.0', $dir = '>=') {
		$this->modx->getVersionData();

		return !empty($this->modx->version) && version_compare($this->modx->version['full_version'], $version, $dir);
	}


	/**
	 * @param modManagerController $controller
	 * @param array $properties
	 */
	public function loadManagerFiles(modManagerController $controller, array $properties = array()) {
		$modx23 = (int)$this->systemVersion();
		$lmsAssetsUrl = $this->config['assetsUrl'];
		$connectorUrl = $this->config['connectorUrl'];
		$lmsCssUrl = $this->config['cssUrl'] . 'mgr/';
		$lmsJsUrl = $this->config['jsUrl'] . 'mgr/';

		if (!empty($properties['config'])) {
			$tmp = array(
				'assets_js' => $lmsAssetsUrl,
				'connector_url' => $connectorUrl,
			);
			$controller->addHtml('<script type="text/javascript">MODx.modx23 = ' . $modx23 . ';LMS.config = ' . $this->modx->toJSON($tmp) . ';</script>', true);
		}
		if (!empty($properties['utils'])) {
			$controller->addJavascript($lmsJsUrl . 'lms.js');
			$controller->addLastJavascript($lmsJsUrl . 'misc/utils.js');
			$controller->addLastJavascript($lmsJsUrl . 'misc/combos.js');
		}
		if (!empty($properties['css'])) {
			$controller->addCss($lmsCssUrl . 'lms.css');
			$controller->addCss($lmsCssUrl . 'bootstrap.buttons.css');
			if (!$modx23) {
				$controller->addCss($lmsCssUrl . 'font-awesome.min.css');
			}
		}

		if (!empty($properties['course'])) {
			$controller->addLastJavascript($lmsJsUrl . 'course/course.common.js');
			$controller->addLastJavascript($lmsJsUrl . 'module/modules.panel.js');
			$controller->addLastJavascript($lmsJsUrl . 'module/modules.grid.js');
			$controller->addLastJavascript($lmsJsUrl . 'test/tests.panel.js');
			$controller->addLastJavascript($lmsJsUrl . 'test/tests.grid.js');
		}
		if (!empty($properties['module'])) {
			$controller->addLastJavascript($lmsJsUrl . 'module/module.common.js');
		}
		if (!empty($properties['test'])) {
			$controller->addLastJavascript($lmsJsUrl . 'test/test.common.js');
		}
		if (!empty($properties['modules'])) {
			$controller->addLastJavascript($lmsJsUrl . 'module/modules.panel.js');
			$controller->addLastJavascript($lmsJsUrl . 'module/modules.grid.js');
		}
		if (!empty($properties['tests'])) {
			$controller->addLastJavascript($lmsJsUrl . 'test/tests.panel.js');
			$controller->addLastJavascript($lmsJsUrl . 'test/tests.grid.js');
		}
		if (!empty($properties['statistic'])) {
			$controller->addLastJavascript($lmsJsUrl . 'statistic/statistic.panel.js');
			$controller->addLastJavascript($lmsJsUrl . 'statistic/statistic.grid.js');
		}
		if (!empty($properties['company'])) {
			$controller->addLastJavascript($lmsJsUrl . 'company/company.panel.js');
			$controller->addLastJavascript($lmsJsUrl . 'company/company.grid.js');
			$controller->addLastJavascript($lmsJsUrl . 'company/company.windows.js');
		}
		if (!empty($properties['profession'])) {
			$controller->addLastJavascript($lmsJsUrl . 'profession/profession.panel.js');
			$controller->addLastJavascript($lmsJsUrl . 'profession/profession.grid.js');
			$controller->addLastJavascript($lmsJsUrl . 'profession/profession.windows.js');
		}
		if (!empty($properties['access'])) {
			$controller->addLastJavascript($lmsJsUrl . 'access/access.panel.js');
			$controller->addLastJavascript($lmsJsUrl . 'access/access.tree.js');
		}
		if (!empty($properties['report'])) {
			$controller->addLastJavascript($lmsJsUrl . 'report/report.panel.js');
			$controller->addLastJavascript($lmsJsUrl . 'report/report.grid.js');
		}
	}

}

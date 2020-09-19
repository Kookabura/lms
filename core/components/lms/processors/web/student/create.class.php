<?php
require_once MODX_CORE_PATH . 'model/modx/processors/security/user/create.class.php';

class StudentCreateProcessor extends modUserCreateProcessor {
	public $languageTopics = array('user', 'lms:default');

	public function initialize() {
        $this->setDefaultProperties(array(
            'email' => $this->getProperty('username'),
            'user.fullname' => $this->getProperty('fullname'),
			'user.email' => $this->getProperty('username'),
            'passwordnotifymethod' => 'e',
            'passwordgenmethod' => 'g'
        ));
        return parent::initialize();
    }

	public function beforeSet() {
		if (!$this->getProperty('groups',null)) return $this->modx->lexicon('student_group_empty');

		$groups = $this->getProperty('groups');
		$role = $this->modx->getObject('modUserGroupRole', array(
			'id' => $groups[0]['role'],
			'authority' => $groups[0]['authority']
			)
		);

		if (!$role) {
			$this->addFieldError('role', $this->modx->lexicon('role_err_wrong_value'));
		}
		else {
			$this->setProperty('role.name', $role->get('name'));
		}

		$set = parent::beforeSet();

		if ($this->hasErrors()) {
			return $this->modx->lexicon('student_err_form');
		}

		return $set;
	}

	/**
     * Send the password notification email, if specified
     * @return void
     */
    public function sendNotificationEmail() {
        if ($this->getProperty('passwordnotifymethod') == 'e') {
            $placeholders = array(
                'uid' => $this->object->get('username'),
                'pwd' => $this->newPassword,
                'ufn' => $this->profile->get('fullname'),
                'sname' => $this->modx->getOption('site_name'),
                'saddr' => $this->modx->getOption('emailsender'),
                'semail' => $this->modx->getOption('emailsender'),
                'surl' => $this->modx->getOption('url_scheme') . $this->modx->getOption('http_host'),
            );
            $message = $this->modx->getChunk($this->modx->getOption('lms.new_student_email_web'), $placeholders);
            $this->object->sendEmail($message);
        }
    }
	
}

return 'StudentCreateProcessor';
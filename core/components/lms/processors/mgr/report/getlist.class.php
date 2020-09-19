<?php

class CourseGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'modUserGroup';
	public $defaultSortField = 'modUserGroup.name';
	public $defaultSortDirection = 'ASC';
	protected $_modx23;


	/**
	 * @return bool
	 */
	public function initialize() {
		$parent = parent::initialize();

		/** @var LMS $LMS */
		$LMS = $this->modx->getService('LMS');
		$this->_modx23 = $LMS->systemVersion();

		return $parent;
	}


	/**
	 * @param xPDOQuery $c
	 *
	 * @return xPDOQuery
	 */
	public function prepareQueryBeforeCount(xPDOQuery $c) {

		$c->select(array(
				$this->modx->getSelectColumns('modUserGroup', 'modUserGroup', '', array('name')),
				//'students' => 'COUNT(UserGroupMembers.member)',
				'pagetitle' => 'Course.pagetitle',
				'id' => 'ResourceGroupResources.id',
				'course_id' => 'Course.id',
				'company_id' => 'modUserGroup.id',
				'roles' => 'TV.value'
			)
		);

		$c->leftJoin('modResourceGroup', 'rGroup', array(
				'modUserGroup.name = rGroup.name'
			)
		);

		$c->leftJoin('modResourceGroupResource', 'ResourceGroupResources', array(
				'rGroup.id = ResourceGroupResources.document_group'
			)
		);

		$c->innerJoin('modResource', 'Course', array(
				'ResourceGroupResources.document = Course.id',
				'Course.class_key = "Course"'
			)
		);

		$c->leftJoin('modTemplateVarResource', 'TV', array(
				'Course.id = TV.contentid',
				'TV.tmplvarid = 4'
			)
		);

		/*$c->leftJoin('modUserGroupMember', 'UserGroupMembers', array(
				'UserGroupMembers.user_group = modUserGroup.id',
				'UserGroupMembers.role IN (REPLACE(TV.value, "||", ","))'
			)
		);*/

		$c->where(array(
			'parent' => $this->modx->getOption('lms.client_parent_group', 0),
		));
		$c->groupby('ResourceGroupResources.id');

		if ($query = $this->getProperty('query', null)) {
			$c->where(array(
				'Course.pagetitle:LIKE' => "%{$query}%",
				'OR:modUserGroup.name:LIKE' => "%{$query}%",
			));
		}

		return $c;
	}

	/**
	 * @param xPDOObject $object
	 *
	 * @return array
	 */
	public function prepareRow(xPDOObject $object) {
		$array = parent::prepareRow($object);

		$this->modx->getContext($array['context_key']);
		$array['preview_url'] = $this->modx->makeUrl($array['id'], $array['context_key']);

		// That's a workaround for roles and users count. Can't handle it prepareQueryBeforeCount. Group Concat wrong return
		$roles = explode('||', $array['roles']);
		$query = $this->modx->newQuery('modUserGroupRole');
		$query->where(array(
				'id:IN' => $roles
			)
		);
		$rows = $this->modx->getIterator('modUserGroupRole', $query);
		foreach ($rows as $role) {
			$result[] = $role->get('name');
		}
		$array['roles'] = implode(', ', $result);

		$roles[] = 2;
		$query = $this->modx->newQuery('modUserGroupMember', array(
				'user_group' => $array['company_id']
				,'role:IN' => $roles
			)
		);

		$array['students'] = $this->modx->getCount('modUserGroupMember', $query);


		$icon = $this->_modx23 ? 'icon' : 'fa';

		return $array;
	}

}

return 'CourseGetListProcessor';
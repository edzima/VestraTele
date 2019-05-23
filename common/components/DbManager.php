<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-05-09
 * Time: 14:58
 */

namespace common\components;

use yii\db\Query;
use yii\rbac\DbManager as BaseDbManager;

class DbManager extends BaseDbManager {

	private $rolesIdsMap = [];

	public function getUserIdsByRole($roleName) {
		if (empty($roleName)) {
			return [];
		}
		if (!isset($this->rolesIdsMap[$roleName])) {
			$this->rolesIdsMap[$roleName] =    (new Query())->select('[[user_id]]')
				->from($this->assignmentTable)
				->where(['item_name' => $roleName])
				->cache(60)
				->column($this->db);
		}
		return $this->rolesIdsMap[$roleName];
	}
}
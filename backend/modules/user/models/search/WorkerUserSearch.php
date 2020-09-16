<?php

namespace backend\modules\user\models\search;

use common\models\user\query\UserQuery;
use common\models\user\Worker;

class WorkerUserSearch extends UserSearch {

	public $parentId;

	public function rules(): array {
		return array_merge(parent::rules(), [
			['parentId', 'integer'],
		]);
	}

	protected function createQuery(): UserQuery {
		return Worker::find();
	}

	public static function getParentsList(): array {
		return [];
	}
}

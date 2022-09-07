<?php

namespace common\modules\lead\fixtures;

use common\modules\lead\Module;
use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture {

	public function init() {
		if (empty($this->modelClass) && empty($this->tableName)) {
			$this->modelClass = Module::userClass();
		}
		parent::init();
	}
}

<?php

namespace common\fixtures\lead;

use common\modules\lead\Module;
use yii\test\ActiveFixture;

class LeadUserFixture extends ActiveFixture {

	public function init() {
		if (empty($this->modelClass) && empty($this->tableName)) {
			$this->modelClass = Module::userClass();
		}
		parent::init();
	}
}

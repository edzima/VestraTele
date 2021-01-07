<?php

namespace common\fixtures;

use common\models\user\UserTrait;
use yii\test\ActiveFixture;

class UserTraitFixture extends ActiveFixture {

	public $modelClass = UserTrait::class;

}

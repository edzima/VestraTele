<?php

namespace common\fixtures\teryt;

use edzima\teryt\models\Simc;
use yii\test\ActiveFixture;

class SimcFixture extends ActiveFixture {

	public $modelClass = Simc::class;

	public $depends = [
		TercFixture::class,
	];
}

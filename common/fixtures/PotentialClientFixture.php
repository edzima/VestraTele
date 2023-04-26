<?php

namespace common\fixtures;

use common\fixtures\teryt\SimcFixture;
use common\models\PotentialClient;
use yii\test\ActiveFixture;

class PotentialClientFixture extends ActiveFixture {

	public $modelClass = PotentialClient::class;

	public $depends = [
		UserFixture::class,
		SimcFixture::class,
	];
}

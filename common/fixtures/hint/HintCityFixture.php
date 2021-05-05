<?php

namespace common\fixtures\hint;

use common\fixtures\teryt\SimcFixture;
use common\fixtures\UserFixture;
use common\models\hint\HintCity;
use yii\test\ActiveFixture;

class HintCityFixture extends ActiveFixture {

	public $modelClass = HintCity::class;

	public $depends = [
		UserFixture::class,
		SimcFixture::class,
	];
}

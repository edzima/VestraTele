<?php

namespace common\fixtures\hint;

use common\models\hint\HintCity;
use common\models\hint\HintCitySource;
use common\models\hint\HintSource;
use yii\test\ActiveFixture;

class HintCitySourceFixture extends ActiveFixture {

	public $modelClass = HintCitySource::class;

	public $depends = [
		HintSource::class,
		HintCity::class,
	];

}

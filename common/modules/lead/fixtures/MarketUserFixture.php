<?php

namespace common\modules\lead\fixtures;

use common\modules\lead\models\LeadMarketUser;
use yii\test\ActiveFixture;

class MarketUserFixture extends ActiveFixture {

	public $modelClass = LeadMarketUser::class;

	public $depends = [
		UserFixture::class,
		MarketFixture::class,
	];

}

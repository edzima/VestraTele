<?php

namespace common\modules\lead\fixtures;

use common\modules\lead\models\LeadMarket;
use yii\test\ActiveFixture;

class MarketFixture extends ActiveFixture {

	public $modelClass = LeadMarket::class;

	public $depends = [
		LeadFixture::class,
	];
}

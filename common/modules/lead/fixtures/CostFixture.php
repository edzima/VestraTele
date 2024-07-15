<?php

namespace common\modules\lead\fixtures;

use common\modules\lead\models\LeadCost;
use yii\test\ActiveFixture;

class CostFixture extends ActiveFixture {

	public $modelClass = LeadCost::class;

	public $depends = [
		CampaignFixture::class,
	];
}

<?php

namespace common\modules\lead\fixtures;

use common\modules\lead\models\LeadCampaign;
use yii\test\ActiveFixture;

class CampaignFixture extends ActiveFixture {

	public $modelClass = LeadCampaign::class;

	public $depends = [
		UserFixture::class,
	];
}

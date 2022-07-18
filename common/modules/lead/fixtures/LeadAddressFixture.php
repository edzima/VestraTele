<?php

namespace common\modules\lead\fixtures;

use common\fixtures\AddressFixture;
use common\modules\lead\models\LeadAddress;
use yii\test\ActiveFixture;

class LeadAddressFixture extends ActiveFixture {

	public $modelClass = LeadAddress::class;

	public $depends = [
		LeadFixture::class,
		AddressFixture::class,
	];
}

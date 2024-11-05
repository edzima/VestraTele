<?php

namespace common\fixtures\provision;

use common\fixtures\settlement\SettlementTypeFixture;
use common\models\provision\ProvisionType;
use yii\test\ActiveFixture;

class ProvisionTypeFixture extends ActiveFixture {

	public $modelClass = ProvisionType::class;

	public $depends = [
		SettlementTypeFixture::class,
	];
}

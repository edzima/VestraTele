<?php

namespace common\fixtures\issue;

use common\models\issue\IssueShipmentPocztaPolska;
use yii\test\ActiveFixture;

class ShipmentsPolskaPocztaFixture extends ActiveFixture {

	public $modelClass = IssueShipmentPocztaPolska::class;

	public $depends = [
		IssueFixture::class,
	];
}

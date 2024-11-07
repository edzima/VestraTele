<?php

namespace common\fixtures\settlement;

use common\fixtures\issue\IssueFixture;
use common\models\issue\IssueCost;
use yii\test\ActiveFixture;

/**
 * Fixture for IssueCost model.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class CostFixture extends ActiveFixture {

	public $modelClass = IssueCost::class;

	public $depends = [
		IssueFixture::class,
		CostTypeFixture::class,
	];
}

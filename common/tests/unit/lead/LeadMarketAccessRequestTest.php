<?php

namespace common\tests\unit\lead;

use common\modules\lead\models\forms\LeadMarketAccessRequest;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use yii\base\Model;

class LeadMarketAccessRequestTest extends Unit {

	use UnitModelTrait;

	private LeadMarketAccessRequest $model;

	public function testEmpty(): void {
		$this->giveModel();
		$this->model->days = 0;

		$this->thenUnsuccessValidate();
		$this->thenSeeError('User Id cannot be blank.', 'user_id');
		$this->thenSeeError('Days cannot be blank.', 'days');
	}

	public function giveModel(array $config = []): void {
		$this->model = new LeadMarketAccessRequest($config);
	}

	public function getModel(): Model {
		return $this->model;
	}
}

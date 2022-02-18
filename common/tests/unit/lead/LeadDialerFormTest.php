<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\forms\LeadDialerForm;
use common\modules\lead\models\LeadDialer;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use yii\helpers\Json;

class LeadDialerFormTest extends Unit {

	use UnitModelTrait;

	private LeadDialerForm $model;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::leads(),
			LeadFixtureHelper::dialer(),
		);
	}

	public function testEmpty(): void {
		$this->giveModel();
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Lead cannot be blank.', 'leadId');
		$this->thenSeeError('Type cannot be blank.', 'typeId');
		$this->thenSeeError('Priority cannot be blank.', 'priority');
		$this->thenSeeError('Next call interval cannot be blank.', 'nextCallInterval');
	}

	public function testValid(): void {
		$this->giveModel();
		$model = $this->getModel();
		$model->leadId = 1;
		$model->typeId = 1;
		$model->priority = LeadDialer::PRIORITY_MEDIUM;
		$model->nextCallInterval = 1200;
		$model->globallyAttemptsLimit = 5;
		$model->dailyAttemptsLimit = 3;
		$this->thenSuccessSave();

		$this->tester->seeRecord(LeadDialer::class, [
			'lead_id' => 1,
			'type_id' => 1,
			'priority' => LeadDialer::PRIORITY_MEDIUM,
			'dialer_config' => Json::encode([
				'dailyAttemptsLimit' => 3,
				'globallyAttemptsLimit' => 5,
				'nextCallInterval' => 1200,
			]),
		]);

		$dialer = $model->getModel();
		$config = $dialer->getConfig();
		$this->tester->assertSame(1200, $config->getNextCallInterval());
		$this->tester->assertSame(5, $config->getGloballyAttemptsLimit());
		$this->tester->assertSame(3, $config->getDailyAttemptsLimit());
	}

	private function giveModel(array $config = []): void {
		$this->model = new LeadDialerForm($config);
	}

	public function getModel(): LeadDialerForm {
		return $this->model;
	}
}

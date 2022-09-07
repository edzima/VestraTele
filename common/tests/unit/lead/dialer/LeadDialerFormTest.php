<?php

namespace common\tests\unit\lead\dialer;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\entities\Dialer;
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
		$this->model->nextCallInterval = null;
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
		$model->typeId = 2;
		$model->priority = LeadDialer::PRIORITY_MEDIUM;
		$model->nextCallInterval = 1200;
		$model->globallyAttemptsLimit = 5;
		$model->dailyAttemptsLimit = 3;
		$this->thenSuccessSave();

		$this->tester->seeRecord(LeadDialer::class, [
			'lead_id' => 1,
			'type_id' => 2,
			'priority' => LeadDialer::PRIORITY_MEDIUM,
			'status' => Dialer::STATUS_NEW,
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

	public function testMultiple(): void {
		$this->giveModel();
		$model = $this->getModel();
		$model->scenario = LeadDialerForm::SCENARIO_MULTIPLE;
		$model->typeId = 3;
		$model->leadId = [1, 2];
		$model->priority = LeadDialer::PRIORITY_MEDIUM;
		$model->nextCallInterval = 1200;
		$model->globallyAttemptsLimit = 5;
		$model->dailyAttemptsLimit = 3;
		$this->tester->assertSame(2, $model->saveMultiple());

		$this->tester->seeRecord(LeadDialer::class, [
			'lead_id' => 1,
			'type_id' => 3,
			'priority' => LeadDialer::PRIORITY_MEDIUM,
			'dialer_config' => Json::encode([
				'dailyAttemptsLimit' => 3,
				'globallyAttemptsLimit' => 5,
				'nextCallInterval' => 1200,
			]),
		]);

		$this->tester->seeRecord(LeadDialer::class, [
			'lead_id' => 2,
			'type_id' => 3,
			'priority' => LeadDialer::PRIORITY_MEDIUM,
			'dialer_config' => Json::encode([
				'dailyAttemptsLimit' => 3,
				'globallyAttemptsLimit' => 5,
				'nextCallInterval' => 1200,
			]),
		]);

		$this->tester->assertSame(0, $model->saveMultiple());
	}

	private function giveModel(array $config = []): void {
		$this->model = new LeadDialerForm($config);
	}

	public function getModel(): LeadDialerForm {
		return $this->model;
	}
}

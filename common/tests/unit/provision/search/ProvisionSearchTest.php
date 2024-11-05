<?php

namespace common\tests\unit\provision\search;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\ProvisionFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\provision\Provision;
use common\models\provision\ProvisionSearch;
use common\models\SearchModel;
use common\tests\_support\UnitSearchModelTrait;
use common\tests\unit\Unit;
use yii\helpers\StringHelper;

/**
 * @property ProvisionSearch $model
 */
class ProvisionSearchTest extends Unit {

	use UnitSearchModelTrait;

	public function _before() {
		parent::_before();
		$this->model = $this->createModel();
	}

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::users(),
			SettlementFixtureHelper::pay(codecept_data_dir() . 'provision/'),
			SettlementFixtureHelper::settlement(codecept_data_dir() . 'provision/'),
			ProvisionFixtureHelper::type(),
			ProvisionFixtureHelper::provision(),
		);
	}

	public function testPayStatuses(): void {
		$this->assertPayStatuses();
	}

	public function testHiddenOnReport(): void {
		$models = $this->search(['hide_on_report' => true])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var Provision $model */
			$this->tester->assertTrue((bool) $model->hide_on_report);
		}
		$this->assertPayStatuses();
	}

	public function testCustomerLastname(): void {
		$this->assertSame(0, $this->search(['customerLastname' => 'TS'])->getTotalCount());
		$this->tester->assertFalse($this->model->validate());
		$this->tester->assertSame(
			'Customer Lastname should contain at least 3 characters.',
			$this->model->getFirstError('customerLastname')
		);
		$models = $this->search([
			'customerLastname' => 'Way',
		])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var Provision $model */
			$this->tester->assertTrue(
				StringHelper::startsWith(
					$model->getIssueModel()->customer->profile->lastname,
					'Way'
				)
			);
		}
		$this->assertPayStatuses();
	}

	public function testDateRangesForPaid(): void {
		$this->model->payStatus = ProvisionSearch::PAY_STATUS_PAID;
		$models = $this->search([
			'dateFrom' => '2020-02-01',
		])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var Provision $model */
			$this->tester->assertGreaterThanOrEqual(strtotime('2020-02-01'), strtotime($model->pay->pay_at));
		}

		$models = $this->search([
			'dateTo' => '2020-02-01',
		])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var Provision $model */
			$this->tester->assertLessThanOrEqual(strtotime('2020-02-01'), strtotime($model->pay->pay_at));
		}

		$models = $this->search([
			'dateFrom' => '2020-01-01',
			'dateTo' => '2020-02-01',
		])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var Provision $model */
			$this->tester->assertGreaterThanOrEqual(strtotime('2020-01-01'), strtotime($model->pay->pay_at));
			$this->tester->assertLessThanOrEqual(strtotime('2020-02-01'), strtotime($model->pay->pay_at));
		}
	}

	public function testDateRangesForUnpaid(): void {
		$this->model->payStatus = ProvisionSearch::PAY_STATUS_UNPAID;
		$models = $this->search([
			'dateFrom' => '2019-01-01',
		])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var Provision $model */
			$this->tester->assertGreaterThanOrEqual(strtotime('2019-01-01'), strtotime($model->pay->deadline_at));
		}

		$models = $this->search([
			'dateTo' => '2020-01-01',
		])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var Provision $model */
			$this->tester->assertLessThanOrEqual(strtotime('2020-01-01'), strtotime($model->pay->deadline_at));
		}

		$models = $this->search([
			'dateFrom' => '2019-01-01',
			'dateTo' => '2019-01-31',
		])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var Provision $model */
			$this->tester->assertGreaterThanOrEqual(strtotime('2019-01-01'), strtotime($model->pay->deadline_at));
			$this->tester->assertLessThanOrEqual(strtotime('2019-01-31'), strtotime($model->pay->deadline_at));
		}
	}

	public function testSettlementType(): void {
		$models = $this->search(['settlementTypes' => [SettlementFixtureHelper::TYPE_ID_HONORARIUM]])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var Provision $model */
			$this->tester->assertSame(SettlementFixtureHelper::TYPE_ID_HONORARIUM, $model->pay->calculation->type_id);
		}
		$models = $this->search([
			'settlementTypes' => [
				SettlementFixtureHelper::TYPE_ID_HONORARIUM,
				SettlementFixtureHelper::TYPE_ID_ADMINISTRATIVE,
			],
		])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			/** @var Provision $model */
			$this->tester->assertContains($model->pay->calculation->type_id, [
				SettlementFixtureHelper::TYPE_ID_HONORARIUM,
				SettlementFixtureHelper::TYPE_ID_ADMINISTRATIVE,
			]);
		}
		$this->assertPayStatuses();
	}

	protected function assertPayStatuses(): void {
		$this->model->payStatus = ProvisionSearch::PAY_STATUS_PAID;
		$models = $this->search()->getModels();
		foreach ($models as $model) {
			/** @var Provision $model */
			$this->tester->assertTrue($model->pay->isPayed());
		}
		$this->model->payStatus = ProvisionSearch::PAY_STATUS_UNPAID;
		$models = $this->search()->getModels();
		foreach ($models as $model) {
			/** @var Provision $model */
			$this->tester->assertFalse($model->pay->isPayed());
		}
	}

	protected function createModel(): SearchModel {
		return new ProvisionSearch(['defaultCurrentMonth' => false]);
	}
}

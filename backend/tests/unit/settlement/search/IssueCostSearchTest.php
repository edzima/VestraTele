<?php

namespace backend\tests\unit\settlement\search;

use backend\modules\settlement\models\search\IssueCostSearch;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\issue\IssueCost;
use common\models\settlement\TransferType;
use common\tests\_support\UnitSearchModelTrait;

/**
 * Class IssueCostSearchTest
 *
 * @property IssueCostSearch $model
 */
class IssueCostSearchTest extends Unit {

	use UnitSearchModelTrait;

	public function _before() {
		parent::_before();
		$this->model = $this->createModel();
		$this->tester->haveFixtures(
			array_merge(
				IssueFixtureHelper::issue(),
				IssueFixtureHelper::stageAndTypesFixtures(),
				SettlementFixtureHelper::settlement(),
				SettlementFixtureHelper::cost(true)
			)
		);
	}

	public function testSettled(): void {
		/** @var IssueCost[] $models */
		$models = $this->search(['settled' => true])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertTrue($model->getIsSettled());
		}
	}

	public function testNotSettled(): void {
		/** @var IssueCost[] $models */
		$models = $this->search(['settled' => false])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertFalse($model->getIsSettled());
		}
	}

	public function testWithSettlements(): void {
		/** @var IssueCost[] $models */
		$models = $this->search(['withSettlements' => true])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertTrue($model->getHasSettlements());
		}
	}

	public function testWithoutSettlements(): void {
		/** @var IssueCost[] $models */
		$models = $this->search(['withSettlements' => false])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertFalse($model->getHasSettlements());
		}
	}

	public function testIssueType(): void {
		/** @var IssueCost[] $models */
		$models = $this->search(['issueType' => 1])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(1, $model->getIssueType()->id);
		}
		/** @var IssueCost[] $models */
		$models = $this->search(['issueType' => 2])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertSame(2, $model->getIssueType()->id);
		}
	}

	public function testConfirmed(): void {
		/** @var IssueCost[] $models */
		$models = $this->search(['is_confirmed' => true])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertTrue($model->getIsConfirmed());
		}
	}

	public function testNotConfirmed(): void {
		/** @var IssueCost[] $models */
		$models = $this->search(['is_confirmed' => false])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertFalse($model->getIsConfirmed());
		}
	}

	public function testTransferType(): void {
		$models = $this->search()->getModels();
		$cash = array_filter($models, static function (TransferType $type): bool {
			return $type->getTransferType() === TransferType::TRANSFER_TYPE_CASH;
		});
		$bank = array_filter($models, static function (TransferType $type): bool {
			return $type->getTransferType() === TransferType::TRANSFER_TYPE_BANK;
		});
		$this->assertNotEmpty($cash);
		$this->assertNotEmpty($bank);
		$models = $this->search([
			'transfer_type' => TransferType::TRANSFER_TYPE_CASH,
		])->getModels();

		foreach ($models as $model) {
			/** @var TransferType $model */
			$this->tester->assertSame(TransferType::TRANSFER_TYPE_CASH, $model->getTransferType());
		}

		$models = $this->search([
			'transfer_type' => TransferType::TRANSFER_TYPE_BANK,
		])->getModels();

		foreach ($models as $model) {
			/** @var TransferType $model */
			$this->tester->assertSame(TransferType::TRANSFER_TYPE_BANK, $model->getTransferType());
		}
	}

	public function testDateRange(): void {
		/** @var IssueCost[] $models */
		$models = $this->search(['dateRange' => '2020-02-10 - 2020-02-11'])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertGreaterThanOrEqual('2020-02-10', $model->date_at);
			$this->tester->assertLessThanOrEqual('2020-02-11', $model->date_at);
		}
	}

	public function testDeadlineRange(): void {
		/** @var IssueCost[] $models */
		$models = $this->search(['deadlineRange' => '2020-03-11 - 2020-03-12'])->getModels();
		$this->tester->assertNotEmpty($models);
		foreach ($models as $model) {
			$this->tester->assertGreaterThanOrEqual('2020-03-11', $model->deadline_at);
			$this->tester->assertLessThanOrEqual('2020-03-12', $model->deadline_at);
		}
	}

	public function testSortRangesAttributes(): void {
		$dataProvider = $this->search();
		$sort = $dataProvider->getSort();
		$this->tester->assertTrue($sort->hasAttribute('dateRange'));
		$this->tester->assertTrue($sort->hasAttribute('deadlineRange'));
		$this->tester->assertTrue($sort->hasAttribute('settledRange'));
	}

	protected function createModel(): IssueCostSearch {
		return new IssueCostSearch();
	}
}

<?php

namespace backend\tests\unit\settlement\search;

use backend\modules\settlement\models\search\PayReceivedSearch;
use backend\tests\unit\Unit;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\SearchModel;
use common\tests\_support\UnitSearchModelTrait;

/**
 * Class CalculationToCreateSearchTest
 *
 * @property PayReceivedSearch $model
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class PayReceivedSearchTest extends Unit {

	use UnitSearchModelTrait;

	public function _before(): void {
		$this->tester->haveFixtures(array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::agent(),
			IssueFixtureHelper::customer(true),
			IssueFixtureHelper::issueUsers(),
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::pay(),
			SettlementFixtureHelper::payReceived(),
		));
		$this->model = $this->createModel();
		parent::_before();
	}

	public function testEmpty(): void {
		$this->assertTotalCount(6);
	}

	public function testAgent(): void {
		$this->model->issueAgent = 300;
		$this->assertTotalCount(5);
		$this->model->issueAgent = 301;
		$this->assertTotalCount(1);
		$this->model->issueAgent = 302;
		$this->assertTotalCount(0);
	}

	public function testUser(): void {
		$this->model->user_id = 300;
		$this->assertTotalCount(2);
		$this->model->user_id = 301;
		$this->assertTotalCount(2);
		$this->model->user_id = 302;
		$this->assertTotalCount(2);
	}

	public function testCustomerLastname(): void {
		$this->model->customerLastname = 'Lar';
		$this->assertTotalCount(1);
		$this->model->customerLastname = 'Way';
		$this->assertTotalCount(5);
	}

	public function testTransferStatus(): void {
		$this->model->transferStatus = PayReceivedSearch::TRANFER_STATUS_NO;
		$this->assertTotalCount(4);
		$this->model->transferStatus = PayReceivedSearch::TRANFER_STATUS_YES;
		$this->assertTotalCount(2);
	}

	protected function createModel(): SearchModel {
		return new PayReceivedSearch();
	}
}

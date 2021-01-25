<?php

namespace common\tests\unit\settlement\search;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\IssuePay;
use common\models\SearchModel;
use common\models\settlement\search\IssuePaySearch;
use common\tests\_support\UnitSearchModelTrait;
use common\tests\unit\Unit;

/**
 * Class PaySearchTest
 *
 * @property IssuePaySearch $model
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class PaySearchTest extends Unit {

	use UnitSearchModelTrait;

	public function _before(): void {
		$this->model = $this->createModel();
		$this->tester->haveFixtures(
			array_merge(
				IssueFixtureHelper::fixtures(),
				IssueFixtureHelper::settlements(),
			));
		parent::_before();
	}

	public function testAll(): void {
		$this->model->payStatus = IssuePaySearch::PAY_STATUS_ALL;
		$this->assertTotalCount(6);
	}

	public function testPayed(): void {
		$this->model->payStatus = IssuePaySearch::PAY_STATUS_PAYED;
		$this->assertTotalCount(3);
		$this->tester->haveRecord(IssuePay::class, [
			'value' => 300,
			'calculation_id' => 1,
			'pay_at' => time(),
		]);
		$this->assertTotalCount(4);
	}

	public function testNotPayed(): void {
		$this->model->payStatus = IssuePaySearch::PAY_STATUS_NOT_PAYED;
		$this->model->delay = IssuePaySearch::DELAY_ALL;
		$this->assertTotalCount(3);
		$this->model->delay = IssuePaySearch::DELAY_NONE;
		$this->assertTotalCount(0);
		$this->tester->haveRecord(IssuePay::class, [
			'value' => 300,
			'calculation_id' => 1,
			'deadline_at' =>date('Y-m-d', strtotime('- 4 days')),
		]);
		$this->model->delay = IssuePaySearch::DELAY_ALL;
		$this->assertTotalCount(4);
		$this->tester->haveRecord(IssuePay::class, [
			'value' => 300,
			'calculation_id' => 1,
			'deadline_at' =>date('Y-m-d', strtotime('+ 4 days')),
		]);
		$this->model->delay = IssuePaySearch::DELAY_NONE;
		$this->assertTotalCount(1);

	}

	public function testAllDelayed(): void {
		$this->model->payStatus = IssuePaySearch::PAY_STATUS_NOT_PAYED;
		$this->model->delay = IssuePaySearch::DELAY_ALL;
		$this->assertTotalCount(3);
		$this->tester->haveRecord(IssuePay::class, [
			'value' => 300,
			'calculation_id' => 1,
			'deadline_at' => time(),
		]);

		$this->assertTotalCount(4);
	}

	public function testMaxDelayedRange(): void {
		$this->model->payStatus = IssuePaySearch::PAY_STATUS_NOT_PAYED;
		$this->model->delay = IssuePaySearch::DELAY_MAX_3_DAYS;
		$this->assertTotalCount(0);
		$this->tester->haveRecord(IssuePay::class, [
			'value' => 300,
			'calculation_id' => 1,
			'deadline_at' => date('Y-m-d', strtotime('- 2 days')),
		]);
		$this->tester->haveRecord(IssuePay::class, [
			'value' => 300,
			'calculation_id' => 1,
			'deadline_at' => date('Y-m-d', strtotime('- 3 days')),
		]);
		$this->assertTotalCount(2);
		$this->tester->haveRecord(IssuePay::class, [
			'value' => 300,
			'calculation_id' => 1,
			'deadline_at' => date('Y-m-d', strtotime('- 4 days')),
		]);

		$this->assertTotalCount(2);
		$this->model->delay = IssuePaySearch::DELAY_MIN_3_MAX_7_DAYS;
		$this->assertTotalCount(2);
		$this->model->delay = IssuePaySearch::DELAY_MIN_7_MAX_14_DAYS;
		$this->assertTotalCount(0);
		$this->tester->haveRecord(IssuePay::class, [
			'value' => 300,
			'calculation_id' => 1,
			'deadline_at' => date('Y-m-d', strtotime('- 7 days')),
		]);
		$this->assertTotalCount(1);
	}

	public function testMinDelayedRange(): void {
		$this->model->payStatus = IssuePaySearch::PAY_STATUS_NOT_PAYED;
		$this->model->delay = IssuePaySearch::DELAY_MIN_30_DAYS;
		$this->assertTotalCount(3);
		$this->tester->haveRecord(IssuePay::class, [
			'value' => 300,
			'calculation_id' => 1,
			'deadline_at' => date('Y-m-d', strtotime('- 30 days')),
		]);
		$this->assertTotalCount(4);
	}


	public function testAgent(): void {

		$this->model->agent_id = UserFixtureHelper::AGENT_PETER_NOWAK;
		$this->model->payStatus = IssuePaySearch::PAY_STATUS_ALL;
		$this->assertTotalCount(4);
		$this->model->agent_id = UserFixtureHelper::AGENT_AGNES_MILLER;

		$this->assertTotalCount(2);
		$this->model->agent_id = [300, 301];
		$this->assertTotalCount(6);
		$this->model->agent_id = 302;
		$this->assertTotalCount(0);

		$this->model->agent_id = 300;
		$this->assertTotalCount(4);
		$this->model->agent_id = 301;
		$this->assertTotalCount(2);
		$this->model->agent_id = [300, 301];
		$this->assertTotalCount(6);
		$this->model->agent_id = 302;
		$this->assertTotalCount(0);
		$this->tester->assertSame('Agent Id is invalid.', $this->model->getFirstError('agent_id'));
		$this->model->agent_id = 303;
		$this->tester->assertSame('Agent Id is invalid.', $this->model->getFirstError('agent_id'));
		$this->assertTotalCount(0);
		$this->model->agent_id = 111111;
		$this->assertTotalCount(0);
		$this->tester->assertSame('Agent Id is invalid.', $this->model->getFirstError('agent_id'));

		$this->model->payStatus = IssuePaySearch::PAY_STATUS_NOT_PAYED;
		$this->model->delay = IssuePaySearch::DELAY_ALL;
		$this->model->agent_id = 300;
		$this->assertTotalCount(3);
		$this->model->agent_id = 301;
		$this->assertTotalCount(0);
		$this->model->agent_id = [300, 301];
		$this->assertTotalCount(3);
		$this->model->agent_id = 302;
		$this->assertTotalCount(0);
	}

	public function testCustomerLastname(): void {
		$this->model->customerLastname = 'Lar';
		$this->assertTotalCount(1);
		$this->model->customerLastname = 'Way';
		$this->assertTotalCount(5);
	}

	public function testWithoutArchive(): void {
		$this->model->withArchive = false;
		$this->assertTotalCount(5);
	}

	protected function createModel(): SearchModel {
		return new IssuePaySearch();
	}
}

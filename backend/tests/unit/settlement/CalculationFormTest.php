<?php

namespace backend\tests\unit\settlement;

use backend\modules\settlement\models\CalculationForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\EmailTemplateFixtureHelper;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use yii\mail\MessageInterface;

class CalculationFormTest extends Unit {

	private SettlementFixtureHelper $settlementFixtureHelper;

	public function _before(): void {
		$this->settlementFixtureHelper = new SettlementFixtureHelper($this->tester);
		parent::_before();
	}

	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::types(),
			IssueFixtureHelper::users(),
			SettlementFixtureHelper::settlement(),
			EmailTemplateFixtureHelper::fixture()
		);
	}

	public function testCreateEmailToCustomerWithoutTemplate(): void {
		$settlement = $this->settlementFixtureHelper->grabSettlement('not-payed-with-double-costs');
		/** @var CalculationForm $model */
		$model = CalculationForm::createFromModel($settlement);
		$this->tester->assertFalse($model->sendCreateEmailToCustomer());
	}

	public function testCreateEmailToWorkersWithoutTemplate(): void {
		$settlement = $this->settlementFixtureHelper->grabSettlement('not-payed-with-double-costs');
		/** @var CalculationForm $model */
		$model = CalculationForm::createFromModel($settlement);
		$this->tester->assertFalse($model->sendCreateEmailToWorkers());
	}

	public function testCustomerEmailToHonorarium(): void {
		$settlement = $this->settlementFixtureHelper->grabSettlement('many-pays-without-costs');
		/** @var CalculationForm $model */
		$model = CalculationForm::createFromModel($settlement);
		$this->tester->assertTrue($model->sendCreateEmailToCustomer());
		$this->tester->seeEmailIsSent();
		/** @var MessageInterface $email */
		$email = $this->tester->grabLastSentEmail();
		$this->tester->assertArrayHasKey($settlement->getIssueModel()->customer->email, $email->getTo());
		$this->tester->assertSame(
			'Create Settlement Honorarium for Customer.',
			$email->getSubject()
		);
	}

	public function testWorkersEmails(): void {
		$settlement = $this->settlementFixtureHelper->grabSettlement('many-pays-without-costs');
		/** @var CalculationForm $model */
		$model = CalculationForm::createFromModel($settlement);
		$this->tester->assertTrue($model->sendCreateEmailToWorkers());
		$this->tester->seeEmailIsSent();
		/** @var MessageInterface $email */
		$email = $this->tester->grabLastSentEmail();
		$this->tester->assertArrayHasKey($settlement->getIssueModel()->agent->email, $email->getTo());
		$this->tester->assertArrayHasKey($settlement->getIssueModel()->tele->email, $email->getTo());
		$this->tester->assertSame(
			'Create Settlement Honorarium for Worker.',
			$email->getSubject()
		);
	}
}

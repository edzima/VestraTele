<?php

namespace backend\tests\unit\settlement;

use backend\modules\settlement\models\CalculationForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\MessageTemplateFixtureHelper;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;

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
			IssueFixtureHelper::users(true),
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::owner(),
			MessageTemplateFixtureHelper::fixture(MessageTemplateFixtureHelper::DIR_ISSUE_SETTLEMENT_CREATE)
		);
	}

	public function testPushMessagesForNotNewRecord(): void {
		$settlement = $this->settlementFixtureHelper->grabSettlement('not-payed-with-double-costs');
		$settlement->isNewRecord = false;

		/** @var CalculationForm $model */
		$model = CalculationForm::createFromModel($settlement);
		$this->tester->assertNull($model->pushMessages());
	}

	public function testMessagesForNewRecord(): void {
		$settlement = $this->settlementFixtureHelper->grabSettlement('many-pays-without-costs');
		$settlement->isNewRecord = true;
		/** @var CalculationForm $model */
		$model = CalculationForm::createFromModel($settlement);

		$this->tester->assertGreaterThan(0, $model->pushMessages(UserFixtureHelper::AGENT_EMILY_PAT));
		$this->tester->seeEmailIsSent();
		$this->tester->seeJobIsPushed();
	}

}

<?php

namespace backend\tests\functional\settlement;

use backend\modules\settlement\controllers\CalculationController;
use backend\tests\FunctionalTester;
use backend\tests\Step\Functional\Bookkeeper;
use common\components\rbac\SettlementTypeAccessManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use common\models\settlement\SettlementType;
use Yii;

class CalculationUpdateCest {

	/**
	 * @see CalculationController::actionUpdate()
	 */
	public const ROUTE = '/settlement/calculation/update';

	private SettlementFixtureHelper $settlementFixture;

	public function _before(Bookkeeper $I): void {
		$this->settlementFixture = new SettlementFixtureHelper($I);

		$I->haveFixtures(array_merge(
			IssueFixtureHelper::issue(),
			IssueFixtureHelper::entityResponsible(),
			IssueFixtureHelper::types(),
			IssueFixtureHelper::customer(),
			IssueFixtureHelper::issueUsers(),
			SettlementFixtureHelper::settlement(),
			SettlementFixtureHelper::type(),
			SettlementFixtureHelper::pay(),
		));
		$I->amLoggedIn();
	}

	public function checkEmailsFieldsVisible(Bookkeeper $I): void {
		$model = $this->settlementFixture->grabSettlement('not-payed-with-double-costs');
		$I->amOnPage([static::ROUTE, 'id' => $model->id]);
		$I->dontSee('Send Email to Customer');
		$I->dontSee('Send Email to Workers');
	}

	public function checkAccessAsBookeeperWithoutPermissions(Bookkeeper $I) {
		$model = $this->settlementFixture->grabSettlement('not-payed-with-double-costs');
		$I->amOnPage([static::ROUTE, 'id' => $model->id]);
		$I->seeResponseCodeIs(403);
	}

	public function checkAccessWithModelManagerPermission(Bookkeeper $I) {
		$this->assignManagerPermission($I);
		$model = $this->settlementFixture->grabSettlement('not-payed-with-double-costs');
		$I->amOnRoute(static::ROUTE, ['id' => $model->id]);
		$I->see('Update settlement: ' . $model->getTypeName());
	}

	public function checkAccessAsDirectlyPermission(Bookkeeper $I) {
		$model = $this->settlementFixture->grabSettlement('not-payed-with-double-costs');
		$model->type->getModelAccess()
			->setAction(SettlementTypeAccessManager::ACTION_CREATE)
			->ensurePermission()
			->assign($I->getUser()->id);

		$I->amOnRoute(static::ROUTE, ['id' => $model->id]);
		$I->see('Update settlement: ' . $model->getTypeName());
	}

	public function checkUpdateValue(Bookkeeper $I): void {
		$model = $this->settlementFixture->grabSettlement('not-payed-with-double-costs');
		$this->assignManagerPermission($I);
		$I->amOnPage([static::ROUTE, 'id' => $model->id]);
		$I->see('Update settlement: ' . $model->getTypeName());
		$I->seeInField('Value with VAT', '1230');
		$I->fillField('Value with VAT', 2460);
		$I->click('Save');
		$I->seeRecord(IssuePayCalculation::class, [
			'id' => $model->id,
			'value' => 2460,
		]);
		$I->dontSeeRecord(IssuePay::class, [
			'calculation_id' => $model->id,
			'value' => 1230,
		]);
		$I->seeRecord(IssuePay::class, [
			'calculation_id' => $model->id,
			'value' => 2460,
		]);
	}

	public function checkUpdateValueForManyPays(Bookkeeper $I): void {
		$calculation = $this->settlementFixture->grabSettlement('many-pays-without-costs');
		$this->assignManagerPermission($I);
		$I->amOnRoute(static::ROUTE, ['id' => $calculation->id]);
		$I->seeInField('Value with VAT', '1230');
		$I->fillField('Value with VAT', 2460);
		$I->click('Save');
		$I->seeRecord(IssuePayCalculation::class, [
			'id' => $calculation->id,
			'value' => 2460,
		]);
		$I->wantTo('See payed Pay.');

		$I->seeRecord(IssuePay::class, [
			'calculation_id' => $calculation->id,
			'value' => 400,
			'pay_at' => '2020-01-01',
		]);
		$I->seeRecord(IssuePay::class, [
			'calculation_id' => $calculation->id,
			'value' => 215,
			'pay_at' => '2020-02-01',
		]);
		$I->seeFlash('Settlement value is not same as sum value from pays. Diff: ' . Yii::$app->formatter->asCurrency(1230) . '.', 'danger');
	}

	public function checkChangeType(Bookkeeper $I): void {
		$this->assignManagerPermission($I);
		$calculation = $this->settlementFixture->grabSettlement('not-payed-with-double-costs');
		$I->amOnRoute(static::ROUTE, ['id' => $calculation->id]);
		$I->seeOptionIsSelected('#calculationform-type_id', 'Honorarium');
		$I->selectOption('#calculationform-type_id', SettlementFixtureHelper::TYPE_ID_ADMINISTRATIVE);
		$I->click('Save');
		$I->dontSeeRecord(IssuePayCalculation::class, [
			'id' => $calculation->id,
			'type_id' => SettlementFixtureHelper::TYPE_ID_HONORARIUM,
		]);
		$I->seeRecord(IssuePayCalculation::class, [
			'id' => $calculation->id,
			'type_id' => SettlementFixtureHelper::TYPE_ID_ADMINISTRATIVE,
		]);
	}

	public function checkUpdateDeadlineAt(Bookkeeper $I): void {
		$this->assignManagerPermission($I);
		$calculation = $this->settlementFixture->grabSettlement('not-payed-with-double-costs');
		$I->amOnRoute(static::ROUTE, ['id' => $calculation->id]);
		$I->see('Update settlement: ' . $calculation->getTypeName());
		$I->seeInField('Deadline at', '2019-01-01');
		$I->fillField('Deadline at', '2021-02-01');
		$I->click('Save');
		$I->dontSeeRecord(IssuePay::class, [
			'calculation_id' => $calculation->id,
			'deadline_at' => '2019-01-01',
		]);
		$I->seeRecord(IssuePay::class, [
			'calculation_id' => $calculation->id,
			'deadline_at' => '2021-02-01',
		]);
	}

	protected function assignManagerPermission(FunctionalTester $I): void {
		$I->assignPermission(
			SettlementType::instance()
				->getModelAccess()->managerPermission
		);
	}

}

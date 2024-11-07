<?php

namespace backend\tests\functional\settlement;

use backend\modules\settlement\controllers\CostTypeController;
use backend\tests\Step\Functional\Bookkeeper;
use backend\tests\Step\Functional\CostTypeManager;
use backend\tests\Step\Functional\Manager;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\settlement\CostType;

class CostTypeCest {

	/** @see CostTypeController::actionIndex() */
	public const ROUTE_INDEX = '/settlement/cost-type/index';

	/** @see CostTypeController::actionCreate() */
	public const ROUTE_CREATE = '/settlement/cost-type/create';

	private const FORM_ID = '#cost-type-form';

	public function _fixtures(): array {
		return SettlementFixtureHelper::costType();
	}

	public function checkAccessAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsBookkeeper(Bookkeeper $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
		$I->seeInTitle('Cost Types');
	}

	public function checkAsCostTypeManager(CostTypeManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIsSuccessful();
		$I->seeInTitle('Cost Types');
	}

	public function createAccess(CostTypeManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_CREATE);
		$I->seeInTitle('Create Cost');
	}

	public function checkCreateEmptyForm(CostTypeManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_CREATE);
		$I->submitForm(static::FORM_ID, $this->formParams('', '', ''));
		$I->seeValidationError('Name cannot be blank.');
	}

	public function checkValidCreate(CostTypeManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_CREATE);
		$I->submitForm(static::FORM_ID, $this->formParams('Test new Name', true, false));
		$I->seeRecord(CostType::class, [
			'name' => 'Test new Name',
			'is_active' => true,
			'is_for_settlement' => false,
		]);
	}

	protected function formParams($name, $is_active, $is_for_settlement): array {
		return [
			'CostTypeForm[name]' => $name,
			'CostTypeForm[is_active]' => $is_active,
			'CostTypeForm[is_for_settlement]' => $is_for_settlement,
		];
	}
}

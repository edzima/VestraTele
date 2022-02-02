<?php

namespace backend\tests\functional\provision;

use backend\modules\provision\controllers\TypeController;
use backend\tests\Step\Functional\Manager;
use backend\tests\Step\Functional\ProvisionManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\ProvisionFixtureHelper;
use common\fixtures\helpers\SettlementFixtureHelper;
use common\models\provision\ProvisionType;

class ProvisionTypeCest {

	/** @see TypeController::actionIndex() */
	public const ROUTE_INDEX = '/provision/type/index';
	/** @see TypeController::actionCreate() */
	public const ROUTE_CREATE = '/provision/type/create';
	/** @see TypeController::actionView() */
	public const ROUTE_VIEW = '/provision/type/view';
	/** @see TypeController::actionSettlement() */
	public const ROUTE_SETTLEMENT = '/provision/type/settlement';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Provisions');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsProvisionManager(ProvisionManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Provisions');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->see('Provisions types', 'h1');
	}

	public function checkCreateLink(ProvisionManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeLink('Create');
		$I->click('Create');
	}

	public function checkIndexGridContent(ProvisionManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeInGridHeader('Name');
		$I->seeInGridHeader('Issue user type');
		$I->seeInGridHeader('Value');
		$I->seeInGridHeader('Is percentage');
		$I->seeInGridHeader('Issue required user types');
		$I->seeInGridHeader('Issue types');
		$I->seeInGridHeader('Is active');
		$I->seeInGridHeader('From at');
		$I->seeInGridHeader('To at');
		$I->seeInGridHeader('User self schema count');
	}

	public function checkSettlementGridContent(ProvisionManager $I): void {
		$I->haveFixtures(array_merge(
			SettlementFixtureHelper::settlement(),
		));
		$I->amLoggedIn();
		$I->amOnPage([static::ROUTE_SETTLEMENT, 'id' => 1]);
		$I->seeInGridHeader('Name');
		$I->seeInGridHeader('Issue user type');
	}

	public function checkCreate(ProvisionManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_CREATE);
		$I->fillField('Name', 'name');
		$I->fillField('Value', 25);
		$I->click('Save');
		$I->see('Name', 'h1');
	}

	public function checkView(ProvisionManager $I): void {
		$I->haveFixtures(array_merge(
			IssueFixtureHelper::types(),
			IssueFixtureHelper::users(),
			ProvisionFixtureHelper::type(),
			ProvisionFixtureHelper::user()
		));
		$I->amLoggedIn();
		/** @var ProvisionType $type */
		$type = $I->grabFixture(ProvisionFixtureHelper::TYPE, 'agent-administrative');
		$I->amOnRoute(static::ROUTE_VIEW, ['id' => $type->id]);
		$I->seeInTitle($type->name);
		$I->seeLink('Update');
		$I->seeLink('Delete');
		$I->seeLink('Create provision schema');
		$I->see('With set type');
		$I->see('Without set type');
	}

	public function checkCreateProvisionLink(ProvisionManager $I): void {
		$I->haveFixtures(array_merge(
			IssueFixtureHelper::types(),
			IssueFixtureHelper::users(),
			ProvisionFixtureHelper::type(),
			ProvisionFixtureHelper::user()
		));
		$I->amLoggedIn();
		/** @var ProvisionType $type */
		$type = $I->grabFixture(ProvisionFixtureHelper::TYPE, 'agent-administrative');
		$I->amOnRoute(static::ROUTE_VIEW, ['id' => $type->id]);
		$I->click('Create provision schema');
		$I->seeInCurrentUrl(ProvisionUserCest::ROUTE_CREATE);
		$I->seeInField('Value', $type->value);
		$I->seeInField('Type', $type->getNameWithValue());
	}
}

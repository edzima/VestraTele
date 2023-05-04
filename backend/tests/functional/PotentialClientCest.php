<?php

namespace backend\tests\functional;

use backend\controllers\PotentialClientController;
use backend\tests\Step\Functional\Manager;
use backend\tests\Step\Functional\PotentialClientManager;
use common\fixtures\PotentialClientFixture;
use common\models\PotentialClient;

class PotentialClientCest {

	/* @see PotentialClientController::actionIndex() */
	public const ROUTE_INDEX = '/potential-client/index';
	/* @see PotentialClientController::actionCreate() */
	public const ROUTE_CREATE = '/potential-client/create';
	private const FORM_SELECTOR = '#form-potential-client';

	public function _fixtures(): array {
		return [
			'potential-client' => [
				'class' => PotentialClientFixture::class,
			],
		];
	}

	public function checkMenuLinkWithoutPermission(Manager $I): void {
		$I->amLoggedIn();
		$I->dontseeMenuLink('Potential Client');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkMenuLinkWithPermission(PotentialClientManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Potential Client');
		$I->clickMenuSubLink('Potential Client');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
	}

	public function checkCreateEmpty(PotentialClientManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->submitForm(static::FORM_SELECTOR, []);
		$I->seeValidationError('Firstname cannot be blank');
		$I->seeValidationError('Lastname cannot be blank');
		$I->seeValidationError('Birthday cannot be blank');
	}

	public function checkCreateWithoutPhone(PotentialClientManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->submitForm(static::FORM_SELECTOR,
			[
				'PotentialClient[firstname]' => 'Elton',
				'PotentialClient[lastname]' => 'Jonn',
				'PotentialClient[birthday]' => '1990-01-01',
			]);
		$I->dontseeValidationError('Firstname cannot be blank');
		$I->dontseeValidationError('Lastname cannot be blank');
		$I->dontseeValidationError('Birthday cannot be blank');
		$I->seeRecord(PotentialClient::class, [
			'firstname' => 'Elton',
			'lastname' => 'Jonn',
			'birthday' => '1990-01-01',
		]);
	}

}

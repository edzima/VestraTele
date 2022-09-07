<?php

namespace backend\tests\functional\hint;

use backend\tests\Step\Functional\HintManager;
use backend\tests\Step\Functional\Manager;
use common\fixtures\hint\HintSourceFixture;
use common\models\hint\HintSource;

class HintSourceCest {

	private const ROUTE_INDEX = '/hint/source/index';
	private const ROUTE_CREATE = '/hint/source/create';
	private const FORM_SELECTOR = '#hint-source-form';

	public function _fixtures(): array {
		return [
			'source' => ['class' => HintSourceFixture::class,],
		];
	}

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Hints');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkAsHintManager(HintManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Hints');
		$I->seeMenuSubLink('Hint Sources');
		$I->clickMenuSubLink('Hint Sources');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
	}

	public function checkIndexGridView(HintManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeInGridHeader('Name');
		$I->seeInGridHeader('Short name');
		$I->seeInGridHeader('Is active');
	}

	public function checkCreate(HintManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_CREATE);
		$I->submitForm(static::FORM_SELECTOR, $this->formParams('Some new source', 'SN', 1));
		$I->seeRecord(HintSource::class, [
			'name' => 'Some new source',
			'short_name' => 'SN',
			'is_active' => 1,
		]);
	}

	private function formParams(string $name, string $shortName, $isActive) {
		return [
			'HintSource[name]' => $name,
			'HintSource[short_name]' => $shortName,
			'HintSource[is_active]' => $isActive,
		];
	}
}

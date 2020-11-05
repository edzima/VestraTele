<?php

namespace backend\tests;

use common\tests\_support\UserRbacActor;
use Facebook\WebDriver\WebDriverKeys;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
 */
class AcceptanceTester extends \Codeception\Actor {

	use _generated\AcceptanceTesterActions;
	use UserRbacActor;

	protected function checkIsLogged(): void {
		$this->wait(1);
		$this->see($this->getUsername());
	}

	public function fillOutSelect2OptionField(string $selector, $value) {
		$I = $this;
		$I->click($selector);
		$searchField = '.select2-search__field';
		$I->waitForElementVisible($searchField);
		$I->fillField($searchField, $value);
		$I->pressKey($searchField, WebDriverKeys::ENTER);
	}

	public function seeValidationError($message): void {
		$this->see($message, '.help-block');
	}

	public function dontSeeValidationError($message): void {
		$this->dontSee($message, '.help-block');
	}

}

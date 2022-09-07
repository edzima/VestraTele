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
		$this->wait(0.2);
		$this->see($this->getUsername());
	}

	public function fillOutSelect2OptionField(string $selector, $value, bool $pressEnter = true) {
		$I = $this;
		$I->click($selector);
		$searchField = '.select2-search__field';
		$I->waitForElementVisible($searchField);
		$I->fillField($searchField, $value);
		if ($pressEnter) {
			$I->wait(1);
			$I->pressKey($searchField, WebDriverKeys::ENTER);
		}
	}

	public function seeInSelect2OptionSearchField(string $selector, $text): void {
		$I = $this;
		$I->click($selector);
		$searchField = '.select2-search__field';
		$I->waitForElementVisible($searchField);
		$I->seeInField($searchField, $text);
		$I->pressKey($searchField, WebDriverKeys::ENTER);
	}

	public function seeValidationError($message): void {
		$this->see($message, '.help-block');
	}

	public function dontSeeValidationError($message): void {
		$this->dontSee($message, '.help-block');
	}

	public function seeImageLoaded(string $element): void {
		$this->waitForElement($element, 5);
		$naturalHeight = $this->grabAttributeFrom($element, 'naturalHeight');
		$this->assertGreaterThan(0, $naturalHeight);
	}

}

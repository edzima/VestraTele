<?php

namespace backend\tests;

use common\tests\_support\UserRbacActor;

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
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
 */
class FunctionalTester extends \Codeception\Actor {

	use _generated\FunctionalTesterActions;
	use UserRbacActor;

	/**
	 * Define custom actions here
	 */

	public function seeValidationError($message): void {
		$this->see($message, '.help-block');
	}

	public function dontSeeValidationError($message): void {
		$this->dontSee($message, '.help-block');
	}

	public function clickMenuLink($text): void {
		$this->click($text, '.main-sidebar li a');
	}

	public function clickMenuSubLink($text): void {
		$this->click($text, '.main-sidebar .treeview-menu li a');
	}

	public function seeMenuLink($text): void {
		$this->see($text, '.main-sidebar li a');
	}

	public function seeMenuSubLink($text): void {
		$this->see($text, '.main-sidebar .treeview-menu li a');
	}

	public function dontSeeMenuLink($text): void {
		$this->dontSee($text, '.main-sidebar li a');
	}

	public function dontSeeMenuSubLink($text): void {
		$this->dontSee($text, '.main-sidebar .treeview-menu li a');
	}

	public function seeInGridHeader($text, string $selector = null): void {
		if ($selector === null) {
			$selector = '.grid-view';
		}
		$selector .= ' th';
		$this->see($text, $selector);
	}

	public function dontSeeInGridHeader($text, string $selector = null): void {
		if ($selector === null) {
			$selector = '.grid-view';
		}
		$selector .= ' th';
		$this->dontSee($text, $selector);
	}

	public function seeFlash(string $text, string $type): void {
		$this->see($text, '.alert.alert-' . $type);
	}

	public function dontSeeFlash(string $text, string $type): void {
		$this->dontSee($text, '.alert.alert-' . $type);
	}
}

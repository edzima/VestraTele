<?php

namespace backend\tests;

use Codeception\Actor;
use Codeception\Lib\Friend;
use common\fixtures\helpers\FixtureTester;
use common\tests\_support\UserRbacActor;
use Yii;

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
 * @method Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
 */
class FunctionalTester extends Actor implements FixtureTester {

	use _generated\FunctionalTesterActions;
	use UserRbacActor;

	private const DEFAULT_GRID_SELECTOR = '.grid-view';

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

	public function seeInGridHeader($text, string $selector = self::DEFAULT_GRID_SELECTOR): void {
		$selector .= ' th';
		$this->see($text, $selector);
	}

	public function dontSeeInGridHeader($text, string $selector = self::DEFAULT_GRID_SELECTOR): void {
		$selector .= ' th';
		$this->dontSee($text, $selector);
	}

	public function seeGridActionLink(string $title, string $selector = self::DEFAULT_GRID_SELECTOR): void {
		$selector .= ' .action-column a';
		$this->seeElement($selector, ['title' => $title]);
	}

	public function seeTitleLink(string $title): void {
		$this->seeElement('a', [
			'title' => $title,
		]);
	}

	public function clickTitleLink(string $title): void {
		$this->click('', 'a[title="' . $title . '"]');
	}

	public function dontSeeGridActionLink(string $title, string $selector = self::DEFAULT_GRID_SELECTOR): void {
		$selector .= ' .action-column a';
		$this->dontSeeElement($selector, ['title' => $title]);
	}

	public function clickGridActionLink(string $title, string $gridSelector = self::DEFAULT_GRID_SELECTOR) {
		$selector = $gridSelector . ' a[title="' . $title . '"]';
		$this->click($selector);
	}

	public function seeFlash(string $text, string $type): void {
		$this->see($text, '.alert.alert-' . $type);
	}

	public function dontSeeFlash(string $text, string $type): void {
		$this->dontSee($text, '.alert.alert-' . $type);
	}

	public function getCSRF(): array {
		return [
			Yii::$app->request->csrfParam => Yii::$app->request->csrfToken,
		];
	}
}

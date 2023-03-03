<?php

namespace frontend\tests;

use Codeception\Actor;
use Codeception\Lib\Friend;
use common\fixtures\helpers\FixtureTester;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\user\Worker;
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

	public function seeValidationError($message): void {
		$this->see($message, '.help-block');
	}

	public function dontSeeValidationError($message): void {
		$this->dontSee($message, '.help-block');
	}

	public function seeMenuLink($link): void {
		$this->see($link, '#main-nav li a');
	}

	public function clickMenuLink($link): void {
		$this->click($link, '#main-nav li a');
	}

	public function dontSeeMenuLink($link): void {
		$this->dontSee($link, '#main-nav li a');
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

	public function seeGridDeleteLink(string $gridSelector, string $actionColumnSelector = '.action-column a', array $attributes = ['title' => 'Delete']): void {
		$selector = $gridSelector . ' ' . $actionColumnSelector;
		$this->seeElement($selector, $attributes);
	}

	public function dontSeeGridDeleteLink(string $gridSelector = '.grid-view', string $actionColumnSelector = '.action-column a', array $attributes = ['title' => 'Delete']): void {
		$selector = $gridSelector . ' ' . $actionColumnSelector;
		$this->dontSeeElement($selector, $attributes);
	}

	public function seeInLoginUrl(): void {
		$this->seeInCurrentUrl('site/login');
	}

	public function grabAgent($index): Worker {
		return $this->grabFixture(IssueFixtureHelper::AGENT, $index);
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

	public function seeGridActionLink(string $title, string $selector = self::DEFAULT_GRID_SELECTOR): void {
		$selector .= ' .action-column a';
		$this->seeElement($selector, ['title' => $title]);
	}

	public function dontSeeGridActionLink(string $title, string $selector = self::DEFAULT_GRID_SELECTOR): void {
		$selector .= ' .action-column a';
		$this->dontSeeElement($selector, ['title' => $title]);
	}

	public function clickGridActionLink(string $title, string $gridSelector = self::DEFAULT_GRID_SELECTOR) {
		$selector = $gridSelector . ' a[title="' . $title . '"]';
		$this->click($selector);
	}

}

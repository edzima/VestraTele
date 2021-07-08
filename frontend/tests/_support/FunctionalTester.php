<?php

namespace frontend\tests;

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
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
 */
class FunctionalTester extends \Codeception\Actor {

	use _generated\FunctionalTesterActions;
	use UserRbacActor;

	public function seeValidationError($message): void {
		$this->see($message, '.help-block');
	}

	public function dontSeeValidationError($message): void {
		$this->dontSee($message, '.help-block');
	}


	public function seeMenuLink($link): void {
		$this->see($link, '#main-nav li a');
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

	public function getCSRF(): array {
		return [
			Yii::$app->request->csrfParam => Yii::$app->request->csrfToken,
		];
	}

}

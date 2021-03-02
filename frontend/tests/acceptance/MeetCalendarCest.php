<?php

namespace frontend\tests\acceptance;

use common\fixtures\helpers\IssueFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\issue\IssueMeet;
use frontend\tests\Step\acceptance\MeetIssueManager;
use yii\helpers\Url;

class MeetCalendarCest {

	public const ROUTE_INDEX = '/meet-calendar/index' . '?agentId=' . UserFixtureHelper::AGENT_PETER_NOWAK;
	public const GorgeSmithsEventSelector = '.fc-time[data-full="10:00 - 11:00"]';
	public const BrandonJonsonEventSelector = '.fc-time[data-full="13:00 - 14:00"]';
	public const EightAMSelector = 'tr[data-time="08:00:00"]';
	public const GorgeSmithsEventSelectorAfterChange = '.fc-time[data-full="8:00 - 9:00"]';
	public const SwalErrorSelector = '.swal2-icon-error';

	public const SignedFilterSelector = '.filter-item[data-id="'.IssueMeet::STATUS_SIGNED_CONTRACT.'"]';
	public const NewFilterSelector = '.filter-item[data-id="'.IssueMeet::STATUS_NEW.'"]';

	/**
	 * Load fixtures before db transaction begin
	 * Called in _before()
	 *
	 * @return array
	 * @see \Codeception\Module\Yii2::loadFixtures()
	 * @see \Codeception\Module\Yii2::_before()
	 */
	public function _fixtures(): array {
		return array_merge(
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::campagin(),
			[
				UserFixtureHelper::profile('agent'),
			]
		);
	}

	public function checkCalendarRoute(MeetIssueManager $I) {
		$I->amLoggedIn();
		$I->amOnPage(Url::toRoute($this::ROUTE_INDEX));
		$I->wait(5);
		$I->see('Kalendarz');
	}

	public function checkSimpleEventVisible(MeetIssueManager $I) {
		$I->amLoggedIn();
		$I->amOnPage(Url::toRoute($this::ROUTE_INDEX));
		$I->waitForCalendarEventsLoaded();
		$I->see('Smith Gorge');
	}

	public function checkCorrectEventDates(MeetIssueManager $I) {
		$I->amLoggedIn();
		$I->amOnPage(Url::toRoute($this::ROUTE_INDEX));
		$I->waitForCalendarEventsLoaded();
		$I->seeElement(self::GorgeSmithsEventSelector);
	}

	public function checkEventClickableTel(MeetIssueManager $I) {
		$I->amLoggedIn();
		$I->amOnPage(Url::toRoute($this::ROUTE_INDEX));
		$I->waitForCalendarEventsLoaded();
		$I->canSeeElement('.tel-link');
		$I->see('Smith Gorge');
	}

	public function checkDragAndDropChangeEventDates(MeetIssueManager $I) {
		$I->amLoggedIn();
		$I->amOnPage(Url::toRoute($this::ROUTE_INDEX));
		$I->waitForCalendarEventsLoaded();
		$I->dragAndDrop(self::GorgeSmithsEventSelector, self::EightAMSelector);
		$I->waitForElement(self::GorgeSmithsEventSelectorAfterChange, 5);
		$I->wait(2);
		$I->cantSeeElement(self::SwalErrorSelector);
	}

	public function checkFilterToggle(MeetIssueManager $I) {
		$I->amLoggedIn();
		$I->amOnPage(Url::toRoute($this::ROUTE_INDEX));
		$I->waitForCalendarEventsLoaded();
		$I->seeElement(self::BrandonJonsonEventSelector);
		$I->seeElement(self::GorgeSmithsEventSelector);

		//disable signed
		$I->click(self::SignedFilterSelector);
		$I->seeElement(self::GorgeSmithsEventSelector);
		$I->cantSeeElement(self::BrandonJonsonEventSelector);

		//also disable new
		$I->click(self::NewFilterSelector);
		$I->cantSeeElement(self::BrandonJonsonEventSelector);
		$I->cantSeeElement(self::GorgeSmithsEventSelector);

		//enable both
		$I->click(self::SignedFilterSelector);
		$I->click(self::NewFilterSelector);
		$I->seeElement(self::BrandonJonsonEventSelector);
		$I->seeElement(self::GorgeSmithsEventSelector);
	}

//	@TODO: implement note testing
}

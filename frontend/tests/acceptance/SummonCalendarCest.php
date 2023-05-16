<?php

namespace frontend\tests\acceptance;

use common\models\issue\Summon;
use frontend\tests\Step\acceptance\SummonIssueManager;
use common\fixtures\helpers\IssueFixtureHelper;
use yii\helpers\Url;

class SummonCalendarCest {

	public const ROUTE_INDEX = '/summon-calendar/index';
	public const PrettyDaySummonSelector = '.fc-time[data-full="7:00"]';
	public const PrettyDaySummonSelectorAfterChange =  '.fc-time[data-full="8:00"]';
	public const PrettyDaySummonTitle = 'agent1';
	public const PrettyDaySummonDeadlineSelector = '.fc-time[data-full="10:00"]';

	public const BeautifulDaySummonSelector = '.fc-time[data-full="11:00"]';

	public const EightAMSelector = 'tr[data-time="08:00:00"]';
	public const SwalErrorSelector = '.swal2-icon-error';

	public const NewFilterSelector = '.filter-item[data-id="' . Summon::STATUS_NEW . '"]';
	public const AppealFilterSelector = '.filter-item[data-id="1"]';
	public const EventFilterSelector = '.filter-item[data-id="event"]';
	public const DeadlineFilterSelector = '.filter-item[data-id="deadline"]';


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
			IssueFixtureHelper::summon(),
		);
	}

	public function checkSummonCalendarRoute(SummonIssueManager $I) {
		$I->amLoggedIn();
		$I->amOnPage(Url::toRoute(self::ROUTE_INDEX));
		$I->see('Kalendarz');
	}

	public function checkSimpleEventVisible(SummonIssueManager $I) {
		$I->amLoggedIn();
		$I->amOnPage(Url::toRoute(self::ROUTE_INDEX));
		$I->waitForCalendarEventsLoaded();
		$I->see(self::PrettyDaySummonTitle);
	}

	public function checkCorrectEventDates(SummonIssueManager $I) {
		$I->amLoggedIn();
		$I->amOnPage(Url::toRoute(self::ROUTE_INDEX));
		$I->waitForCalendarEventsLoaded();
		$I->seeElement(self::PrettyDaySummonSelector);
	}

	public function checkSimpleDeadlineVisible(SummonIssueManager $I) {
		$I->amLoggedIn();
		$I->amOnPage(Url::toRoute(self::ROUTE_INDEX));
		$I->waitForCalendarEventsLoaded();
		$I->seeElement(self::PrettyDaySummonDeadlineSelector);
	}

	public function checkDragAndDropChangeEventDates(SummonIssueManager $I) {
		$I->amLoggedIn();
		$I->amOnPage(Url::toRoute(self::ROUTE_INDEX));
		$I->waitForCalendarEventsLoaded();
		$I->dragAndDrop(self::PrettyDaySummonSelector, self::EightAMSelector);
		$I->waitForElement(self::PrettyDaySummonSelectorAfterChange, 5);
		$I->wait(2);
		$I->cantSeeElement(self::SwalErrorSelector);
	}

	public function checkFilterToggle(SummonIssueManager $I) {
		$I->amLoggedIn();
		$I->amOnPage(Url::toRoute(self::ROUTE_INDEX));
		$I->waitForCalendarEventsLoaded();
		$I->seeElement(self::PrettyDaySummonSelector);
		$I->seeElement(self::BeautifulDaySummonSelector);
		$I->seeElement(self::PrettyDaySummonDeadlineSelector);

		$I->click(self::NewFilterSelector); // HIDE
		$I->cantSeeElement(self::PrettyDaySummonSelector);
		$I->seeElement(self::BeautifulDaySummonSelector);
		$I->seeElement(self::PrettyDaySummonDeadlineSelector);
		$I->click(self::NewFilterSelector); // SHOW

		$I->seeElement(self::BeautifulDaySummonSelector);
		$I->seeElement(self::PrettyDaySummonSelector);

		$I->click(self::AppealFilterSelector); // HIDE
		$I->cantSeeElement(self::PrettyDaySummonSelector);
		$I->cantSeeElement(self::BeautifulDaySummonSelector);
		$I->seeElement(self::PrettyDaySummonDeadlineSelector);
		$I->click(self::AppealFilterSelector); // SHOW

		$I->seeElement(self::BeautifulDaySummonSelector);
		$I->seeElement(self::PrettyDaySummonSelector);
		$I->seeElement(self::PrettyDaySummonDeadlineSelector);

		$I->click(self::EventFilterSelector); // HIDE
		$I->cantSeeElement(self::PrettyDaySummonSelector);
		$I->cantSeeElement(self::BeautifulDaySummonSelector);
		$I->seeElement(self::PrettyDaySummonDeadlineSelector);
		$I->click(self::EventFilterSelector); // SHOW


		$I->click(self::DeadlineFilterSelector); // HIDE
		$I->seeElement(self::PrettyDaySummonSelector);
		$I->seeElement(self::BeautifulDaySummonSelector);
		$I->cantSeeElement(self::PrettyDaySummonDeadlineSelector);
		$I->click(self::DeadlineFilterSelector); // SHOW

		$I->seeElement(self::BeautifulDaySummonSelector);
		$I->seeElement(self::PrettyDaySummonSelector);

		$I->click(self::NewFilterSelector); // HIDE
		$I->click(self::AppealFilterSelector); // HIDE
		$I->click(self::EventFilterSelector); // HIDE

		$I->cantSeeElement(self::BeautifulDaySummonSelector);
		$I->cantSeeElement(self::PrettyDaySummonSelector);

		$I->click(self::NewFilterSelector); // ENABLE
		$I->cantSeeElement(self::BeautifulDaySummonSelector);
		$I->cantSeeElement(self::PrettyDaySummonSelector);

		$I->click(self::AppealFilterSelector); // ENABLE
		$I->cantSeeElement(self::BeautifulDaySummonSelector);
		$I->cantSeeElement(self::PrettyDaySummonSelector);

		$I->click(self::EventFilterSelector); // SHOW
		$I->seeElement(self::BeautifulDaySummonSelector);
		$I->seeElement(self::PrettyDaySummonSelector);
	}

}

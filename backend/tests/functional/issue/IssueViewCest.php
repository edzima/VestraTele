<?php

namespace backend\tests\functional\issue;

use backend\helpers\Url;
use backend\modules\issue\controllers\IssueController;
use backend\tests\Step\Functional\CostIssueManager;
use backend\tests\Step\Functional\CreateCalculationIssueManager;
use backend\tests\Step\Functional\IssueManager;
use backend\tests\Step\Functional\SummonIssueManager;
use common\fixtures\helpers\IssueFixtureHelper;
use common\models\issue\Issue;
use common\models\user\Worker;

class IssueViewCest {

	/** @see IssueController::actionView() */
	public const ROUTE = '/issue/issue/view';
	/** @see IssueController::actionDelete() */
	public const ROUTE_DELETE = '/issue/issue/delete';

	/**
	 * Load fixtures before db transaction begin
	 * Called in _before()
	 *
	 * @return array
	 * @see \Codeception\Module\Yii2::loadFixtures()
	 * @see \Codeception\Module\Yii2::_before()
	 */
	public function _fixtures(): array {
		return IssueFixtureHelper::fixtures();
	}

	public function checkPage(IssueManager $I): void {
		$I->amLoggedIn();
		$model = $this->goToIssuePage($I);
		$I->seeInTitle($model->longId);
		$I->see($model->customer->getFullName());
		$I->see($model->type->name);
		$I->see($model->stage->name);
		$I->see($model->lawyer->getFullName());
	}

	public function checkLinksAsManager(IssueManager $I): void {
		$I->amLoggedIn();
		$this->goToIssuePage($I);
		$I->seeLink('Update');
		$I->dontSeeLink('Costs');
		$I->dontSeeLink('Create settlement');
		$I->dontSeeLink('Create Summon');
		$I->dontSeeLink('Create Provision');
	}

	public function checkProvisionLink(IssueManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(ClaimCest::PERMISSION);
		$this->goToIssuePage($I);
		$I->seeLink('Create Provision');
		$I->click('Create Provision');
		$I->seeInCurrentUrl(ClaimCest::ROUTE_CREATE);
	}

	public function checkNoteLink(IssueManager $I): void {
		$I->assignNotePermission();
		$I->amLoggedIn();
		$this->goToIssuePage($I);
		$I->seeLink('Create note');
		$I->click('Create note');
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkStageLink(IssueManager $I): void {
		$I->amLoggedIn();
		$this->goToIssuePage($I);
		$I->seeLink('Change Stage');
		$I->click('Change Stage');
		$I->seeInCurrentUrl(IssueStageChangeCest::ROUTE);
	}

	public function checkCostLink(CostIssueManager $I): void {
		$I->amLoggedIn();
		$this->goToIssuePage($I);
		$I->seeLink('Costs');
		$I->click('Costs');
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkCreateSettlementLink(CreateCalculationIssueManager $I): void {
		$I->amLoggedIn();
		$this->goToIssuePage($I);
		$I->seeLink('Create settlement');
		$I->click('Create settlement');
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkCreateSummonLink(SummonIssueManager $I): void {
		$I->amLoggedIn();
		$this->goToIssuePage($I);
		$I->seeLink('Create Summon');
		$I->click('Create Summon');
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkLinkUserLinkWithPermission(IssueManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(Worker::PERMISSION_ISSUE_LINK_USER);
		$this->goToIssuePage($I);
		$I->seeLink('Link User');
		$I->click('Link User');
		$I->seeResponseCodeIsSuccessful();
		$I->seeInCurrentUrl(LinkUserCest::ROUTE_LINK);
	}

	public function checkDeleteLinkWithoutPermission(IssueManager $I): void {
		$I->amLoggedIn();
		$model = $this->goToIssuePage($I);
		$I->dontSeeLink('Delete');
		$I->sendAjaxPostRequest(Url::to([static::ROUTE_DELETE, 'id' => $model->id]), $I->getCSRF());
		$I->seeResponseCodeIs(403);
	}

	public function checkDeleteLinkWithPermission(IssueManager $I): void {
		$I->amLoggedIn();
		$I->assignPermission(Worker::PERMISSION_ISSUE_DELETE);
		$model = $this->goToIssuePage($I);
		$I->seeLink('Delete');
		$I->sendAjaxPostRequest(Url::to([static::ROUTE_DELETE, 'id' => $model->id]), $I->getCSRF());
		$I->dontSeeRecord(Issue::class, [
			'id' => $model->id,
		]);
	}

	protected function goToIssuePage(IssueManager $I, string $issueIndex = '0'): Issue {
		/** @var Issue $model */
		$model = $I->grabFixture(IssueFixtureHelper::ISSUE, $issueIndex);
		$I->amOnPage([static::ROUTE, 'id' => $model->id]);
		return $model;
	}

}

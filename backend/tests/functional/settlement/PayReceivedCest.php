<?php

namespace backend\tests\functional\settlement;

use backend\modules\settlement\controllers\PayReceivedController;
use backend\tests\Step\Functional\Manager;
use backend\tests\Step\Functional\PayReceivedManager;
use common\fixtures\helpers\IssueFixtureHelper;
use Yii;

class PayReceivedCest {

	/** @see PayReceivedController::actionIndex() */
	public const ROUTE_INDEX = '/settlement/pay-received/index';

	/** @see PayReceivedController::actionReceive() */
	public const ROUTE_RECEIVE_PAYS = '/settlement/pay-received/receive';

	protected const FORM_SELECTOR = '#receive-pays-form';

	public function checkAsManager(Manager $I): void {
		$I->amLoggedIn();
		$I->dontSeeMenuLink('Received pays');
	}

	public function checkAsPayReceivedManager(PayReceivedManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Received pays');
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeInTitle('Received pays');
		$I->seeInGridHeader('Settlement type');
		$I->seeInGridHeader('Receiver');
		$I->seeInGridHeader('Agent');
		$I->seeInGridHeader('Customer');
		$I->seeInGridHeader('Receive At');
		$I->seeInGridHeader('Transfer At');
		$I->seeInGridHeader('Value with VAT');
	}

	public function checkReceiveLink(PayReceivedManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_INDEX);
		$I->seeLink('Receive');
		$I->click('Receive');
		$I->seeResponseCodeIsSuccessful();
	}

	public function checkReceivePays(PayReceivedManager $I): void {
		$I->haveFixtures(array_merge(
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::settlements(),
			IssueFixtureHelper::payReceived()
		));
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_RECEIVE_PAYS);
		$I->see('Receive pays');
		$I->submitForm(static::FORM_SELECTOR, $this->formParams(300, [1], '2020-12-12'));
		$I->seeFlash('Received 1 pays. Sum value: ' . Yii::$app->formatter->asCurrency(1230), 'success');
	}

	public function checkReceivePaysEmptyForm(PayReceivedManager $I): void {
		$I->haveFixtures(array_merge(
			IssueFixtureHelper::fixtures(),
			IssueFixtureHelper::settlements(),
			IssueFixtureHelper::payReceived()
		));
		$I->amLoggedIn();
		$I->amOnPage(static::ROUTE_RECEIVE_PAYS);
		$I->see('Receive pays');
		$I->click('Save');
		$I->seeValidationError('Receiver cannot be blank.');
		$I->seeValidationError('Pays cannot be blank.');
	}

	protected function formParams($user_id, $pays_ids, $date): array {
		return [
			'ReceivePaysForm' => [
				'user_id' => $user_id,
				'pays_ids' => $pays_ids,
				'date' => $date,
			],
		];
	}
}

<?php

namespace frontend\tests\functional;

use common\fixtures\helpers\LeadFixtureHelper;
use frontend\controllers\ApiLeadController;
use frontend\tests\FunctionalTester;
use Yii;

class ApiLeadCest {

	/** @see ApiLeadController::actionLanding() */
	private const ROUTE_LANDING = '/api-lead/landing';

	public function _fixtures(): array {
		return LeadFixtureHelper::leads();
	}

	public function checkLanding(FunctionalTester $I): void {
		$I->sendAjaxPostRequest(static::ROUTE_LANDING, [
			'source_id' => 1,
			'email' => 'email@example.com',
		]);
		$I->seeRecord(Yii::$app->leadManager->model, [
			'source_id' => 1,
			'email' => 'email@example.com',
		]);
	}
}

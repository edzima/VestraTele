<?php

namespace frontend\tests\functional;

use common\fixtures\helpers\LeadFixtureHelper;
use frontend\controllers\LeadController;
use frontend\tests\FunctionalTester;
use Yii;

class LeadCest {

	/** @see LeadController::actionLanding() */
	private const ROUTE_LANDING = '/lead/landing';

	public function _fixtures(): array {
		return LeadFixtureHelper::leads();
	}

	public function checkLanding(FunctionalTester $I): void {
		$I->sendAjaxPostRequest(static::ROUTE_LANDING, [
			'source_id' => 1,
			'type_id' => 1,
			'email' => 'email@example.com',
		]);
		$I->seeRecord(Yii::$app->lead->model, [
			'source_id' => 1,
			'type_id' => 1,
			'email' => 'email@example.com',
		]);
	}
}

<?php

namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;
use Yii;

class LeadCest {

	private const ROUTE_LANDING = '/lead/landing';

	public function checkLanding(FunctionalTester $I): void {
		$I->sendAjaxPostRequest(static::ROUTE_LANDING, ['source' => 'landing']);
		$I->seeRecord(Yii::$app->lead->model, [
			'source' => 'landing',
		]);
	}
}

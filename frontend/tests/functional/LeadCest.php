<?php

namespace frontend\tests\functional;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\LeadEntity;
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
			LeadEntity::SOURCE_DATA_ATTRIBUTE => 'landing',
			LeadEntity::EMAIL_DATA_ATTRIBUTE => 'email@example.com',
			LeadEntity::TYPE_DATA_ATTRIBUTE => 1,
		]);
		$I->seeRecord(Yii::$app->lead->model, [
			'source' => 'landing',
			'email' => 'email@example.com',
		]);
	}
}

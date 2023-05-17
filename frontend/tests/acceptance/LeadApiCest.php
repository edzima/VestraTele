<?php

namespace frontend\tests\acceptance;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\Lead;
use frontend\tests\AcceptanceTester;
use Yii;

class LeadApiCest {

	public function _fixtures(): array {
		return LeadFixtureHelper::leads();
	}

	public function checkCustomer(AcceptanceTester $I): void {
		Yii::$app->leadClient->addFromCustomer([
			'name' => 'Test name',
			'phone' => '+48 123 123 123',
			'source_id' => 1,
			'date_at' => '2020-01-01 12:00:00',
		]);
		$I->seeRecord(Lead::class, [
			'name' => 'Test name',
			'source_id' => 1,
			'provider' => Lead::PROVIDER_CRM_CUSTOMER,
		]);
	}
}

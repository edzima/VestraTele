<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\forms\LeadMarketAccessRequest;
use common\modules\lead\models\LeadMarketUser;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use yii\base\Model;

class LeadMarketAccessRequestTest extends Unit {

	use UnitModelTrait;

	private LeadMarketAccessRequest $model;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::lead(),
			LeadFixtureHelper::market(),
			LeadFixtureHelper::user()
		);
	}

	public function testEmpty(): void {
		$this->giveModel();
		$this->thenUnsuccessValidate();
		$this->thenSeeError('User Id cannot be blank.', 'user_id');
		$this->thenDontSeeError('days');
		$this->thenDontSeeError('details');
	}

	public function testDetailsRequiredWhenDaysNotDefault(): void {
		$this->giveModel([
			'days' => LeadMarketAccessRequest::DEFAULT_DAYS + 1,
		]);

		$this->thenUnsuccessValidate();
		$this->thenSeeError('Details cannot be blank when Days is other than: ' . LeadMarketAccessRequest::DEFAULT_DAYS . '.', 'details');
	}

	public function testCreateNew(): void {
		$market = $this->tester->grabFixture(LeadFixtureHelper::MARKET, 0);

		$this->giveModel([
			'market' => $market,
		]);

		$this->model->user_id = 2;
		$this->thenSuccessSave();

		$this->tester->seeRecord(LeadMarketUser::class, [
			'user_id' => 2,
			'market_id' => $market->id,
			'days_reservation' => LeadMarketAccessRequest::DEFAULT_DAYS,
		]);

		$this->tester->seeEmailIsSent();
		$email = $this->tester->grabLastSentEmail();

		$this->tester->assertMessageBodyContainsString(
			'Accept',
			$email
		);

		$this->tester->assertMessageBodyContainsString(
			'Reject',
			$email
		);
	}

	public function testUpdate(): void {
		/** @var LeadMarketUser $marketUser */
		$marketUser = $this->tester->grabFixture(LeadFixtureHelper::MARKET_USER, 0);
		$this->giveModel([
			'model' => $marketUser,
		]);
		$this->model->details = 'New Details Updated';
		$this->thenSuccessSave();
		$this->tester->seeRecord(LeadMarketUser::class, [
			'user_id' => $marketUser->user_id,
			'market_id' => $marketUser->market_id,
			'details' => 'New Details Updated',
		]);

		$this->tester->dontSeeEmailIsSent();
	}

	public function giveModel(array $config = []): void {
		$this->model = new LeadMarketAccessRequest($config);
	}

	public function getModel(): Model {
		return $this->model;
	}
}

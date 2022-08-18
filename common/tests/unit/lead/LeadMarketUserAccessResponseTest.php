<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\forms\LeadMarketAccessResponseForm;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadMarketUser;
use common\modules\lead\models\LeadUser;
use common\tests\unit\Unit;
use yii\mail\MessageInterface;

class LeadMarketUserAccessResponseTest extends Unit {

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::lead(),
			LeadFixtureHelper::market(),
			LeadFixtureHelper::user(),
		);
	}

	public function testGenerateReservedAt(): void {
		$model = new LeadMarketUser();
		$model->days_reservation = 2;
		$model->generateReservedAt(strtotime('2020-01-01'));
		$this->assertSame('2020-01-03', $model->reserved_at);
	}

	public function testGenerateReservedAtForEmptyDays(): void {
		$model = new LeadMarketUser();
		$model->days_reservation = null;
		$model->generateReservedAt();
		$this->assertNull($model->reserved_at);
	}

	public function testAccepted(): void {
		$model = new LeadMarketUser();
		$model->market_id = 2;
		$model->days_reservation = 2;
		$model->user_id = 3;

		$responseModel = $this->giveModel($model);
		$this->tester->assertNotNull($responseModel->accept());

		$this->tester->assertSame(LeadMarketUser::STATUS_ACCEPTED, $model->status);
		$this->tester->assertSame($this->generateReservedAt(2), $model->reserved_at);
		$this->tester->assertSame(LeadMarket::STATUS_BOOKED, $model->market->status);
	}

	public function testRejected(): void {
		$model = new LeadMarketUser();
		$model->reserved_at = '2020-02-02';
		$responseForm = $this->giveModel($model);
		$responseForm->reject();
		$this->tester->assertSame(LeadMarketUser::STATUS_REJECTED, $model->status);
		$this->assertNull($model->reserved_at);
	}

	public function testAddUserAlreadyHasAccessToLead(): void {
		$this->tester->wantTo('User is Already in Lead.');
		$model = new LeadMarketUser();
		$model->market_id = 1;
		$model->user_id = 1;
		$responseForm = $this->giveModel($model);
		$this->tester->assertNull($responseForm->linkUserToLead());
		$model->user_id = 2;
		$this->tester->assertNull($responseForm->linkUserToLead());
		$model->user_id = 3;
		$this->tester->assertNull($responseForm->linkUserToLead());
	}

	public function testAddUser(): void {
		$this->tester->wantTo('User is Already in Lead.');
		$model = new LeadMarketUser();
		$model->market_id = 2;
		$model->user_id = 1;
		$responseForm = $this->giveModel($model);

		$this->tester->assertSame(LeadUser::TYPE_MARKET_FIRST, $responseForm->linkUserToLead());
		$this->tester->assertNull($responseForm->linkUserToLead());
		$model->user_id = 3;
		$this->tester->assertSame(LeadUser::TYPE_MARKET_SECOND, $responseForm->linkUserToLead());
	}

	public function testAcceptEmailForNotAcceptedModel(): void {
		$model = $this->giveModel(new LeadMarketUser([
			'market_id' => 1,
			'user_id' => 1,
			'status' => LeadMarketUser::STATUS_TO_CONFIRM,
		]));

		$this->tester->assertFalse($model->sendAcceptEmail());
		$this->tester->dontSeeEmailIsSent();
	}

	public function testAcceptEmailForAcceptedModel(): void {
		$model = $this->giveModel(new LeadMarketUser([
			'market_id' => 1,
			'user_id' => 1,
			'status' => LeadMarketUser::STATUS_ACCEPTED,
			'reserved_at' => $this->generateReservedAt(2),
		]));

		$this->tester->assertTrue($model->sendAcceptEmail());
		$this->tester->seeEmailIsSent();
		/** @var MessageInterface */
		$mail = $this->tester->grabLastSentEmail();
		$this->tester->assertSame('Your Access Request is Accepted.', $mail->getSubject());
	}

	public function testRejectEmailForRejecteddModel(): void {
		$model = $this->giveModel(new LeadMarketUser([
			'market_id' => 1,
			'user_id' => 1,
			'status' => LeadMarketUser::STATUS_REJECTED,
		]));

		$this->tester->assertTrue($model->sendRejectEmail());
		$this->tester->seeEmailIsSent();
		/** @var MessageInterface */
		$mail = $this->tester->grabLastSentEmail();
		$this->tester->assertSame('Your Access Request is Rejected.', $mail->getSubject());
	}

	private function generateReservedAt(int $days): string {
		return date('Y-m-d', strtotime("+ $days days"));
	}

	private function giveModel(LeadMarketUser $model): LeadMarketAccessResponseForm {
		return new LeadMarketAccessResponseForm($model);
	}

}

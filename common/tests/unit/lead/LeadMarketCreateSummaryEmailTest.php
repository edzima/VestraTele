<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\forms\LeadMarketCreateSummaryEmail;
use common\modules\lead\models\LeadMarket;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use yii\base\Model;
use yii\mail\MessageInterface;

class LeadMarketCreateSummaryEmailTest extends Unit {

	use UnitModelTrait;

	private LeadMarketCreateSummaryEmail $model;

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::lead(),
			LeadFixtureHelper::market(),
			LeadFixtureHelper::user()
		);
	}

	public function testEmptyDatabase(): void {
		LeadMarket::deleteAll();
		$this->giveModel();
		$model = new LeadMarketCreateSummaryEmail();
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Emails cannot be blank.', 'emails');
		$this->tester->assertNull($model->sendEmail());
	}

	public function testEmpty(): void {
		$this->giveModel();
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Created From cannot be blank.', 'createdFrom');
		$this->thenSeeError('Created To cannot be blank.', 'createdTo');
		$this->tester->assertSame($this->model->getMarketUsersEmails(), $this->model->emails);
	}

	public function testYesterdayScenario(): void {
		$this->giveModel();
		$this->model->scenario = LeadMarketCreateSummaryEmail::SCENARIO_YESTERDAY;
		$this->thenSuccessValidate();

		$this->tester->assertNull($this->model->sendEmail());

		$this->haveMarket(date('Y-m-d H:i:s', strtotime('-1 day')));
		$this->haveMarket(date('Y-m-d H:i:s', strtotime('-1 day')));
		$this->haveMarket(date('Y-m-d H:i:s', strtotime('-2 day')));
		$this->haveMarket(date('Y-m-d H:i:s'));
		$this->tester->assertSame(1, $this->model->sendEmail());
		/** @var MessageInterface $mail */
		$mail = $this->tester->grabLastSentEmail();
		$this->tester->assertSame('New 2 Leads on Market.', $mail->getSubject());
	}

	private function haveMarket(string $created_at): int {
		$model = new LeadMarket([
			'status' => LeadMarket::STATUS_BOOKED,
			'creator_id' => 1,
			'lead_id' => 1,
			'created_at' => $created_at,
		]);
		$model->detachBehavior('time');
		$model->created_at = $created_at;
		$model->save();
		return $model->id;
	}

	public function getModel(): Model {
		return $this->model;
	}

	public function giveModel(array $config = []): void {
		$this->model = new LeadMarketCreateSummaryEmail($config);
	}
}

<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\forms\LeadsUserForm;
use common\modules\lead\models\LeadUser;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;

class LeadsUserFormTest extends Unit {

	use UnitModelTrait;

	private LeadsUserForm $model;

	public function _before() {
		parent::_before();
		$this->model = new LeadsUserForm();
	}

	public function _fixtures(): array {
		return array_merge(
			LeadFixtureHelper::lead(),
			LeadFixtureHelper::user(),
		);
	}

	public function testEmpty(): void {
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Leads Ids cannot be blank.', 'leadsIds');
		$this->thenSeeError('Type cannot be blank.', 'type');
		$this->thenSeeError('User cannot be blank.', 'userId');
	}

	public function testInvalidUser(): void {
		$this->model->userId = 1000;
		$this->thenUnsuccessValidate();
		$this->thenSeeError('User is invalid.', 'userId');
	}

	public function testInvalidType(): void {
		$this->model->type = 'not-existed-type';
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Type is invalid.', 'type');
	}

	public function testNotExistedLeadsIds(): void {
		$this->model->leadsIds = [10000, 20000];
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Leads Ids is invalid.', 'leadsIds');
	}

	public function testWithoutOverwrite(): void {
		$this->model->leadsIds = [1, 2];
		$this->model->userId = 1;
		$this->model->type = LeadUser::TYPE_TELE;
		$this->thenSaveCount(2);
		$this->thenSeeLeadUser(1, 1, LeadUser::TYPE_TELE);
		$this->thenSeeLeadUser(2, 1, LeadUser::TYPE_TELE);
	}

	public function testOverwrite(): void {
		//	codecept_debug(Lead::find()->asArray()->all());
		$this->model->leadsIds = [1, 2];
		$this->model->userId = 1;
		$this->model->type = LeadUser::TYPE_AGENT;
		$this->thenSuccessValidate();
		$this->thenSaveCount(2);
		$this->thenSeeLeadUser(1, 1, LeadUser::TYPE_AGENT);
		$this->thenDontSeeLeadUser(1, 2, LeadUser::TYPE_AGENT);
	}

	public function getModel(): LeadsUserForm {
		return $this->model;
	}

	private function thenSeeLeadUser(int $leadId, int $user_id, string $type) {
		$this->tester->seeRecord(LeadUser::class, [
			'lead_id' => $leadId,
			'user_id' => $user_id,
			'type' => $type,
		]);
	}

	private function thenDontSeeLeadUser(int $leadId, int $user_id, string $type) {
		$this->tester->dontSeeRecord(LeadUser::class, [
			'lead_id' => $leadId,
			'user_id' => $user_id,
			'type' => $type,
		]);
	}

	private function thenSaveCount(int $count) {
		$this->tester->assertSame($count, $this->model->save());
	}
}

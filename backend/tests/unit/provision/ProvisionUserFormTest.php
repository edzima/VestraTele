<?php

namespace backend\tests\unit\provision;

use backend\modules\provision\models\ProvisionUserForm;
use backend\tests\unit\Unit;
use common\fixtures\helpers\ProvisionFixtureHelper;
use common\fixtures\helpers\UserFixtureHelper;
use common\models\provision\ProvisionType;
use common\models\provision\ProvisionUser;
use common\tests\_support\UnitModelTrait;
use yii\base\Model;

class ProvisionUserFormTest extends Unit {

	use UnitModelTrait;

	private ProvisionUserForm $form;

	public function _before() {
		parent::_before();
		$this->tester->haveFixtures(
			array_merge(
				UserFixtureHelper::workers(),
				ProvisionFixtureHelper::type(),
				ProvisionFixtureHelper::user(),
			)
		);
	}

	public function testEmpty(): void {
		$this->givenForm();
		$this->thenUnsuccessValidate();
		$this->thenSeeError('From cannot be blank.', 'from_user_id');
		$this->thenSeeError('To cannot be blank.', 'to_user_id');
		$this->thenSeeError('Type cannot be blank.', 'type_id');
		$this->thenSeeError('Value cannot be blank.', 'value');
	}

	public function testCreateSelf(): void {
		$this->givenForm([
			'from_user_id' => UserFixtureHelper::AGENT_TOMMY_SET,
			'to_user_id' => UserFixtureHelper::AGENT_TOMMY_SET,
			'value' => 50,
			'type_id' => 1,
		]);

		$this->thenSuccessSave();
		$this->thenSeeProvisionUser([
			'from_user_id' => UserFixtureHelper::AGENT_TOMMY_SET,
			'to_user_id' => UserFixtureHelper::AGENT_TOMMY_SET,
			'value' => 50,
			'type_id' => 1,
		]);
	}

	public function testMaxValue(): void {
		$percentageValueType = $this->tester->grabRecord(ProvisionType::class, [
			'is_percentage' => 1,
		])->id;

		$notPercentageValueType = $this->tester->grabRecord(ProvisionType::class, [
			'is_percentage' => 0,
		])->id;


		$this->givenForm([
			'from_user_id' => UserFixtureHelper::AGENT_TOMMY_SET,
			'to_user_id' => UserFixtureHelper::AGENT_TOMMY_SET,
			'value' => 50,
			'type_id' => $percentageValueType,
		]);

		$this->thenSuccessValidate();

		$form = $this->form;

		$form->value = 100;
		$this->thenSuccessValidate();

		$form->value = 0;
		$this->thenSuccessValidate();

		$form->value = -1;
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Value must be no less than 0.', 'value');

		$form->value = 101;
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Value must be no greater than 100.', 'value');

		$form->type_id = $notPercentageValueType;
		$form->value = 101;
		$this->thenSuccessValidate();
	}

	public function testInvalidDateRange(): void {
		$this->givenForm([
			'from_user_id' => UserFixtureHelper::AGENT_TOMMY_SET,
			'to_user_id' => UserFixtureHelper::AGENT_TOMMY_SET,
			'value' => 50,
			'type_id' => 1,
			'from_at' => '2020-01-01',
			'to_at' => '2019-01-01',
		]);
		$this->thenUnsuccessSave();
		$this->thenSeeError('To at must be greater than or equal to "From at".', 'to_at');
	}

	public function testDateAsSameDay(): void {
		$this->givenForm([
			'from_user_id' => UserFixtureHelper::AGENT_TOMMY_SET,
			'to_user_id' => UserFixtureHelper::AGENT_TOMMY_SET,
			'value' => 50,
			'type_id' => 1,
			'from_at' => '2020-01-01',
			'to_at' => '2020-01-01',
		]);
		$this->thenSuccessSave();
		$this->thenSeeProvisionUser([
			'from_user_id' => UserFixtureHelper::AGENT_TOMMY_SET,
			'to_user_id' => UserFixtureHelper::AGENT_TOMMY_SET,
			'value' => 50,
			'type_id' => 1,
			'from_at' => '2020-01-01',
			'to_at' => '2020-01-01',
		]);
	}

	public function testCreateAgain(): void {
		$this->givenForm([
			'from_user_id' => UserFixtureHelper::AGENT_TOMMY_SET,
			'to_user_id' => UserFixtureHelper::AGENT_TOMMY_SET,
			'value' => 50,
			'type_id' => 1,
		]);
		$this->thenSuccessSave();

		$this->givenForm([
			'from_user_id' => UserFixtureHelper::AGENT_TOMMY_SET,
			'to_user_id' => UserFixtureHelper::AGENT_TOMMY_SET,
			'value' => 50,
			'type_id' => 1,
		]);
		$this->thenUnsuccessValidate();
		$this->thenSeeError('From at cannot be blank.', 'from_at');
	}

	private function givenForm(array $config = []): void {
		$this->form = new ProvisionUserForm($config);
	}

	private function thenSeeProvisionUser(array $attributes): void {
		$this->tester->seeRecord(ProvisionUser::class, $attributes);
	}

	public function getModel(): Model {
		return $this->form;
	}

}

<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\forms\LeadQuestionForm;
use common\modules\lead\models\LeadQuestion;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use yii\base\Model;

class LeadQuestionFormTest extends Unit {

	use UnitModelTrait;

	private LeadQuestionForm $model;

	public function _fixtures(): array {
		return LeadFixtureHelper::question();
	}

	public function testEmpty(): void {
		$this->giveForm([]);
		$this->model->type = '';
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Name cannot be blank.', 'name');
		$this->thenSeeError('Type cannot be blank.', 'type');
	}

	public function testOnlyName(): void {
		$this->giveForm([
			'name' => 'Some schema name',
		]);
		$this->thenSuccessSave();
		$this->thenSeeRecord([
			'name' => 'Some schema name',
			'type' => LeadQuestion::TYPE_TEXT,
		]);
	}

	public function testRadioTypeWithoutValues(): void {
		$this->giveForm([
			'name' => 'Some radio group',
			'type' => LeadQuestion::TYPE_RADIO_GROUP,
		]);
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Values cannot be blank.', 'values');
	}

	public function testRadioTypeWithValues(): void {
		$this->giveForm([
			'name' => 'Are you like ice cream?',
			'type' => LeadQuestion::TYPE_RADIO_GROUP,
			'values' => [
				'yes',
				'sometimes',
				'no',
			],
		]);
		$this->thenSuccessSave();
		$this->thenSeeRecord([
			'name' => 'Are you like ice cream?',
			'placeholder' => 'yes|sometimes|no',
		]);
	}

	public function testWithStatus(): void {
		$this->giveForm([
			'name' => 'Some schema name',
			'status_id' => '1',
		]);
		$this->thenSuccessSave();
		$this->thenSeeRecord([
			'name' => 'Some schema name',
			'status_id' => '1',
		]);
	}

	public function testWithType(): void {
		$this->giveForm([
			'name' => 'Some schema name',
			'type_id' => '1',
		]);
		$this->thenSuccessSave();
		$this->thenSeeRecord([
			'name' => 'Some schema name',
			'type_id' => '1',
		]);
	}

	private function thenSeeRecord(array $attributes): void {
		$this->tester->seeRecord(LeadQuestion::class, $attributes);
	}

	private function giveForm(array $config): void {
		$this->model = new LeadQuestionForm($config);
	}

	public function getModel(): Model {
		return $this->model;
	}
}

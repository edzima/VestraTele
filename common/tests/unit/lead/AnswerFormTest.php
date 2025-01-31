<?php

namespace common\tests\unit\lead;

use common\fixtures\helpers\LeadFixtureHelper;
use common\modules\lead\models\forms\AnswerForm;
use common\modules\lead\models\LeadAnswer;
use common\modules\lead\models\LeadQuestion;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use yii\base\Model;

class AnswerFormTest extends Unit {

	use UnitModelTrait;

	private const DEFAULT_REPORT_ID = 1;

	private AnswerForm $model;
	private LeadQuestion $question;

	public function _fixtures(): array {
		return LeadFixtureHelper::reports();
	}

	public function testEmptyAnswerWithRequiredQuestion(): void {
		$this->giveQuestion();
		$this->question->is_required = true;
		$this->giveModel();
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Firstname cannot be blank.', 'answer');
	}

	public function testRequiredWithAnswer(): void {
		$this->giveQuestion();
		$this->question->is_required = true;
		$this->giveModel();
		$this->model->answer = 'John';
		$this->thenSuccessValidate();
	}

	public function testSaveTextWithEmptyText(): void {
		$this->giveQuestion();
		$this->giveModel();
		$this->model->answer = '';
		$this->thenUnsuccessSave();
		$this->dontSeeAnswer(['answer' => '']);
	}

	public function testUpdateTextAsEmpty() {
		$this->giveQuestion();
		$this->giveModel();
		$this->model->answer = 'Johnny';

		$this->thenSuccessSave();

		$answer = $this->model->getModel();
		$this->giveModel();
		$this->model->setModel($answer);
		$this->model->answer = '';

		$this->thenSuccessSave();

		$this->dontSeeAnswer([
			'answer' => 'Johnny',
		]);
	}

	public function testUpdate(): void {
		$this->giveQuestion();
		$this->giveModel();
		$this->model->answer = 'John1';
		$this->thenSuccessSave();
		$this->seeAnswer([
			'answer' => 'John1',
		]);
		$answer = $this->model->getModel();

		$this->giveModel();
		$this->model->setModel($answer);
		$this->model->answer = 'John2';
		$this->thenSuccessSave();

		$this->dontSeeAnswer([
			'answer' => 'John1',
		]);
		$this->seeAnswer([
			'answer' => 'John2',
		]);
	}

	public function testRadioGroupQuestionWithValidAnswer(): void {
		$this->giveQuestion(
			'Do you like Winter?',
			LeadQuestion::TYPE_RADIO_GROUP
		);
		$this->assertEmpty($this->question->getRadioValues());
		$this->question->setRadioValues(['yes', 'no', 'sometimes']);
		$this->giveModel();
		$this->model->answer = 'yes';
		$this->thenSuccessValidate();
		$this->thenSuccessSave();
		$this->seeAnswer([
			'answer' => 'yes',
			'question_id' => $this->model->getQuestion()->id,
		]);
	}

	public function testRadioGroupQuestionWithInvalidAnswer(): void {
		$this->giveQuestion(
			'Do you like Winter?',
			LeadQuestion::TYPE_RADIO_GROUP
		);
		$this->assertEmpty($this->question->getRadioValues());
		$this->question->setRadioValues(['yes', 'no', 'sometimes']);
		$this->giveModel();
		$this->model->answer = 'never';
		$this->thenUnsuccessValidate();
		$this->thenSeeError('Do you like Winter? is invalid.', 'answer');
	}

	private function giveQuestion(string $name = 'Firstname', string $type = LeadQuestion::TYPE_TEXT): void {
		$this->question = new LeadQuestion([
			'name' => $name,
			'type' => $type,
		]);
	}

	private function giveModel(): void {
		$this->model = new AnswerForm($this->question);
		$this->model->report_id = static::DEFAULT_REPORT_ID;
	}

	public function getModel(): Model {
		return $this->model;
	}

	private function seeAnswer(array $array) {
		if (!isset($array['report_id'])) {
			$array['report_id'] = static::DEFAULT_REPORT_ID;
		}
		$this->tester->seeRecord(LeadAnswer::class, $array);
	}

	private function dontSeeAnswer(array $array) {
		if (!isset($array['report_id'])) {
			$array['report_id'] = static::DEFAULT_REPORT_ID;
		}
		$this->tester->dontSeeRecord(LeadAnswer::class, $array);
	}
}

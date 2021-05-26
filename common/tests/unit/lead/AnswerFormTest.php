<?php

namespace common\tests\unit\lead;

use common\modules\lead\models\forms\AnswerForm;
use common\modules\lead\models\LeadQuestion;
use common\tests\_support\UnitModelTrait;
use common\tests\unit\Unit;
use yii\base\Model;

class AnswerFormTest extends Unit {

	use UnitModelTrait;

	private AnswerForm $model;
	private LeadQuestion $question;

	public function testEmptyAnswerWithRequiredQuestion(): void {
		$this->giveQuestion('Firstname', 'Input firstname');
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

	private function giveQuestion(string $name = 'Firstname', ?string $placeholder = null): void {
		$this->question = new LeadQuestion([
			'name' => $name,
			'placeholder' => $placeholder,
		]);
	}

	private function giveModel(): void {
		$this->model = new AnswerForm($this->question);
	}

	public function getModel(): Model {
		return $this->model;
	}
}

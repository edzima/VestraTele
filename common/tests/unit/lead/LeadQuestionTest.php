<?php

namespace common\tests\unit\lead;

use common\modules\lead\models\LeadQuestion;
use common\tests\unit\Unit;
use Yii;

class LeadQuestionTest extends Unit {

	public function testAnswerQuestionWithoutPlaceholder(): void {
		$question = new LeadQuestion([
			'name' => 'Is worker',
		]);

		$this->tester->assertSame('Is worker', $question->generateAnswer());
	}

	public function testAnswerQuestionWithPlaceholderWithAnswer(): void {
		$question = new LeadQuestion([
			'name' => 'Firstname',
			'placeholder' => 'Firstname',
		]);

		$this->tester->assertSame('Firstname: John', $question->generateAnswer('John'));
	}

	public function testAnswerQuestionWithPlaceholderWithoutAnswer(): void {
		$question = new LeadQuestion([
			'name' => 'Firstname',
			'placeholder' => 'Firstname',
		]);

		$this->tester->assertSame('Firstname: ' . Yii::$app->formatter->nullDisplay, $question->generateAnswer());
	}
}

<?php

namespace common\tests\unit\lead;

use common\modules\lead\models\LeadQuestion;
use common\tests\unit\Unit;
use Yii;

class LeadQuestionTest extends Unit {

	public function testAnswerForTag(): void {
		$question = new LeadQuestion([
			'name' => 'Is worker',
			'type' => LeadQuestion::TYPE_TAG,
		]);

		$this->tester->assertSame('Is worker', $question->getQuestionWithAnswer(true));
		$this->tester->assertSame(Yii::$app->formatter->nullDisplay, $question->getQuestionWithAnswer());
		$this->tester->assertSame(Yii::$app->formatter->nullDisplay, $question->getQuestionWithAnswer(false));
	}

	public function testAnswerForTextQuestion(): void {
		$question = new LeadQuestion([
			'name' => 'Firstname',
			'type' => LeadQuestion::TYPE_TEXT,
		]);

		$this->tester->assertSame('Firstname: John', $question->getQuestionWithAnswer('John'));
	}

	public function testAnswerForTextWithoutAnswer(): void {
		$question = new LeadQuestion([
			'name' => 'Firstname',
			'type' => LeadQuestion::TYPE_TEXT,
		]);

		$this->tester->assertSame('Firstname: ' . Yii::$app->formatter->nullDisplay, $question->getQuestionWithAnswer());
	}

	public function testRadioGroupValues(): void {
		$question = new LeadQuestion([
			'name' => 'Do you like winter?',
			'type' => LeadQuestion::TYPE_RADIO_GROUP,
		]);

		$this->assertEmpty($question->getRadioValues());
		$question->setRadioValues(['yes', 'no', 'sometimes']);

		$values = $question->getRadioValues();
		$this->tester->assertCount(3, $values);
		$this->tester->assertContains('yes', $values);
		$this->tester->assertContains('no', $values);
		$this->tester->assertContains('sometimes', $values);

		$question->setRadioValues([0, 1, 2]);
		$this->tester->assertCount(3, $values);

	}
}

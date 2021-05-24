<?php

namespace common\tests\unit\lead;

use common\modules\lead\models\LeadAnswer;
use common\modules\lead\models\query\LeadAnswerQuery;
use common\tests\unit\Unit;

class LeadAnswerQueryTest extends Unit {

	private LeadAnswerQuery $query;

	public function _before(): void {
		parent::_before();
		$this->query = LeadAnswer::find();
	}

	public function testLikeAnswersWithEmptyArray(): void {
		$this->whenLikeAnswers([]);
		$this->thenSqlTextNotContains('WHERE');
	}

	public function testLikeAnswerWithSingleStringAnswer(): void {
		$this->whenLikeAnswers([
			1 => 'some answer',
		]);
		$this->thenSqlTextContains("WHERE (`question_id`=1) AND (`answer` LIKE '%some answer%')");
	}

	public function testLikeAnswerWithTrueAnswer(): void {
		$this->whenLikeAnswers([
			1 => true,
		]);
		$this->thenSqlTextContains("`question_id`=1");
		$this->thenSqlTextNotContains('LIKE');
	}

	public function testLikeAnswerWithFalseAnswer(): void {
		$this->whenLikeAnswers([
			1 => false,
		]);
		$this->thenSqlTextContains("WHERE (`question_id`=1)");
	}

	private function thenSqlTextContains(string $needle): void {
		$this->tester->assertStringContainsString($needle, $this->getRawSql());
	}

	private function thenSqlTextNotContains(string $needle): void {
		$this->tester->assertStringNotContainsString($needle, $this->getRawSql());
	}

	private function getRawSql(): string {
		return $this->query->createCommand()->getRawSql();
	}

	private function whenLikeAnswers(array $answers): void {
		$this->query->likeAnswers($answers);
	}
}

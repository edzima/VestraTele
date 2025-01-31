<?php

namespace common\modules\lead\widgets;

use common\modules\lead\models\LeadAnswer;
use common\modules\lead\Module;
use Yii;
use yii\base\Widget;
use yii\widgets\DetailView;

class LeadAnswersWidget extends Widget {

	/**
	 * @var LeadAnswer[]
	 */
	public array $answers = [];

	public ?bool $deletedReportFilter = null;

	public function init(): void {
		if ($this->deletedReportFilter === null) {
			$this->deletedReportFilter = Module::manager()->onlyForUser;
		}
		if ($this->deletedReportFilter) {
			$this->answers = $this->getAnswersWithoutDeletedReports($this->answers);
		}
		parent::init();
	}

	public function run(): string {

		if (empty($this->answers)) {
			return '';
		}
		return DetailView::widget([
			'model' => false,
			'attributes' => $this->getDetailViewAttributes(),
		]);
	}

	private function getDetailViewAttributes(): array {
		$attributes = [];
		$tags = [];
		LeadAnswer::orderByQuestions($this->answers);
		foreach ($this->answers as $answer) {
			if ($answer->question->isTag()) {
				$tags[] = $answer->getAnswerQuestion();
			} else {
				$attributes[] = [
					'label' => $answer->question->name,
					'value' => $answer->getAnswerQuestion(),
				];
			}
		}
		if (!empty($tags)) {
			$attributes[] = [
				'label' => Yii::t('lead', 'Tags'),
				'value' => implode(', ', $tags),
			];
		}
		return $attributes;
	}

	private function getAnswersWithoutDeletedReports(array $answers): array {
		return array_filter($answers, function (LeadAnswer $answer): bool {
			return !$answer->report->isDeleted();
		});
	}

}

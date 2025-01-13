<?php

namespace common\modules\lead\models\query;

use common\modules\lead\models\LeadInterface;
use common\modules\lead\models\LeadQuestion;
use yii\db\ActiveQuery;

class LeadQuestionQuery extends ActiveQuery {

	public function forLead(LeadInterface $lead): self {
		$this->forStatus($lead->getStatusId());
		$this->forType($lead->getSource()->getType()->id);
		return $this;
	}

	public function active(): self {
		$this->andWhere(['is_active' => true]);
		return $this;
	}

	public function required(): self {
		$this->andWhere(['is_required' => true]);
		return $this;
	}

	public function notRequired(): self {
		$this->andWhere(['is_required' => false]);
		return $this;
	}

	public function forStatus(int $status_id): self {
		$this->andWhere(['or', ['status_id' => null], ['status_id' => $status_id]]);
		return $this;
	}

	public function forType(int $type_id): self {
		$this->andWhere(['or', ['type_id' => null], ['type_id' => $type_id]]);
		return $this;
	}

	public function showInGrid(): self {
		$this->andWhere(['show_in_grid' => true]);
		return $this;
	}

	public function withPlaceholder(): self {
		$this->andWhere('placeholder IS NOT NULL');
		return $this;
	}

	public function withoutPlaceholder(): self {
		$this->andWhere(['placeholder' => null]);
		return $this;
	}

	public function tags(): self {
		$this->andWhere(['type' => LeadQuestion::TYPE_TAG]);
		return $this;
	}

	public function onlyAnswered(): self {
		$this->joinWith([
			'answers' => function (LeadAnswerQuery $answerQuery): void {
				$answerQuery->andWhere('question_id IS NOT NULL');
			},
		], false);
		return $this;
	}

	public function boolean(bool $boolean): self {
		$this->andWhere(['is_boolean' => $boolean]);
		return $this;
	}

	/**
	 * {@inheritDoc}
	 * @return LeadQuestion|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}

	/**
	 * {@inheritDoc}
	 * @return LeadQuestion[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

}

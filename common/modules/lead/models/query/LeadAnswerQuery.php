<?php

namespace common\modules\lead\models\query;

use common\modules\lead\models\LeadAnswer;
use yii\db\ActiveQuery;

class LeadAnswerQuery extends ActiveQuery {

	public function likeAnswers(array $answers): self {
		foreach ($answers as $question_id => $answer) {
			if (is_string($answer)) {
				$this->orWhere([
					'and',
					['question_id' => $question_id],
					['like', 'answer', $answer],
				]);
			} else {
				if ($answer) {
					$this->andWhere(['question_id' => $question_id]);
				} else {
					$this->andWhere(['question_id' => null]);
				}
			}
		}
		return $this;
	}

	/**
	 * {@inheritDoc}
	 * @return LeadAnswer|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}

	/**
	 * {@inheritDoc}
	 * @return LeadAnswer[]
	 */
	public function all($db = null) {
		return parent::all($db);
	}
}

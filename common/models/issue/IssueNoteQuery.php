<?php

namespace common\models\issue;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[IssueNote]].
 *
 * @see IssueNote
 */
class IssueNoteQuery extends ActiveQuery {

	public function onlyPays(): self {
		return $this->onlyType(IssueNote::TYPE_PAY);
	}

	public function onlyType(int $type): self {
		$this->andWhere(['type' => $type]);
		return $this;
	}

	/**
	 * @inheritdoc
	 * @return IssueNote[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return IssueNote|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}
}

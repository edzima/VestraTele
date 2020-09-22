<?php

namespace common\models\issue\query;

use common\models\issue\IssueNote;
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

	public function onlySummon(int $summonId): self {
		$this->onlyType(IssueNote::TYPE_SUMMON, $summonId);
		return $this;
	}

	public function onlyType(string $type, int $id = null): self {
		if ($id !== null) {
			$this->andWhere(['type' => IssueNote::generateType($type, $id)]);
		} else {
			$this->andWhere(['like', 'type', $type]);
		}
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

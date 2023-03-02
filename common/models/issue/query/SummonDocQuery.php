<?php

namespace common\models\issue\query;

use common\models\issue\SummonDoc;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[SummonDoc]].
 *
 * @see SummonDoc
 */
class SummonDocQuery extends ActiveQuery {

	/**
	 * @inheritdoc
	 * @return SummonDoc[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return SummonDoc|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}

	public function summonTypes(array $typesIds) {
		if (!empty($typesIds)) {
			$this->andWhere(['REGEXP', 'summon_types', implode('|', $typesIds)]);
		}
		return $this;
	}
}

<?php

namespace common\models\provision;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Provision]].
 *
 * @see Provision
 */
class ProvisionQuery extends ActiveQuery {

	public function hidden(): self {
		$this->andWhere(['hide_on_report' => true]);
		return $this;
	}

	/**
	 * {@inheritdoc}
	 * @return Provision[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * {@inheritdoc}
	 * @return Provision|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}
}

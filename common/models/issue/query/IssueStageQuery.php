<?php

namespace common\models\issue\query;

use common\models\issue\IssueStage;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Issue]].
 *
 * @see Issue
 */
class IssueStageQuery extends ActiveQuery {

	public function init() {
		parent::init();
		$this->addOrderBy('posi');
	}

	/**
	 * @inheritdoc
	 * @return IssueStage[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return IssueStage|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}
}

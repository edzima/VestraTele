<?php

namespace common\modules\lead\models\query;

use common\models\query\PhonableQuery;
use common\models\query\PhonableQueryTrait;
use common\modules\lead\models\Lead;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for Lead.
 *
 * @see Lead
 */
class LeadQuery extends ActiveQuery implements PhonableQuery {

	use PhonableQueryTrait;

	/**
	 * @inheritdoc
	 * @return Lead[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return Lead|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}
}

<?php

namespace common\modules\lead\models\query;

use common\modules\lead\models\LeadCampaign;
use yii\db\ActiveQuery;

class LeadCampaignQuery extends ActiveQuery {

	/**
	 * @inheritdoc
	 * @return LeadCampaign[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return LeadCampaign|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}

}

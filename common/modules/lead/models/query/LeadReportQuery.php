<?php

namespace common\modules\lead\models\query;

use common\modules\lead\models\LeadReport;
use common\modules\lead\models\ReportSchemaInterface;
use yii\db\ActiveQuery;

class LeadReportQuery extends ActiveQuery {

	/**
	 * {@inheritDoc}
	 * @return LeadReport|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}

	/**
	 * {@inheritDoc}
	 * @return LeadReport[]
	 */
	public function all($db = null) {
		return parent::all($db);
	}
}

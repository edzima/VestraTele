<?php

namespace common\modules\lead\models\query;

use common\modules\lead\models\LeadReport;
use common\modules\lead\models\ReportSchemaInterface;
use yii\db\ActiveQuery;

class LeadReportQuery extends ActiveQuery {


	public function likeFirstname(string $firstname): void {
		$this->orWhere([
			'and',
			['like', '%' . $firstname, true],
			['schema_id' => ReportSchemaInterface::FIRSTNAME_ID],
		]);
	}

	public function likeLastname(string $lastname): void {
		$this->orWhere([
			'and',
			['like', '%' . $lastname, true],
			['schema_id' => ReportSchemaInterface::LASTNAME_ID],
		]);
	}


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

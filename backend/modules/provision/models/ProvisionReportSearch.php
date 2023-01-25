<?php

namespace backend\modules\provision\models;

use common\models\provision\ProvisionReportSearch as BaseProvisionReportSearch;

class ProvisionReportSearch extends BaseProvisionReportSearch {

	public function rules(): array {
		return array_merge(parent::rules(), [
			[['withoutEmpty'], 'boolean'],
			['excludedFromUsers','safe']
		]);
	}
}

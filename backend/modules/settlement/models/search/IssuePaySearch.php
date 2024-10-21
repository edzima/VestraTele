<?php

namespace backend\modules\settlement\models\search;

use common\models\settlement\search\IssuePaySearch as BaseIssuePaySearch;

class IssuePaySearch extends BaseIssuePaySearch {

	public bool $settlementAccessManagerRequired = true;

	public function rules(): array {
		return array_merge([
			['!userId', 'required'],
		], parent::rules());
	}

}

<?php

namespace backend\modules\settlement\models\search;

use common\components\rbac\SettlementTypeAccessManager;
use common\models\settlement\search\IssuePayCalculationSearch as BaseIssuePayCalculationSearch;

/**
 * Base IssuePayCalculationSearch for Backend App.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class IssuePayCalculationSearch extends BaseIssuePayCalculationSearch {

	public string $action = SettlementTypeAccessManager::ACTION_INDEX;

	public function __construct($userId, array $config = []) {
		parent::__construct($config);
		$this->userId = $userId;
	}

	public function rules(): array {
		return array_merge(
			[
				[['!userId', '!action'], 'required',],
			]
			, parent::rules()
		);
	}

}

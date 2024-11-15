<?php

namespace backend\modules\settlement\models\search;

use common\components\rbac\SettlementTypeAccessManager;

/**
 * Base IssuePayCalculationSearch for Backend App.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class IssueViewPayCalculationSearch extends IssuePayCalculationSearch {

	public string $action = SettlementTypeAccessManager::ACTION_ISSUE_VIEW;

	public ?bool $is_percentage = null;

	public function __construct(int $issueId, $userId, array $config = []) {
		parent::__construct($userId, $config);
		$this->issue_id = $issueId;
	}

	public function rules(): array {
		return array_merge(
			[
				[['!issue_id'], 'required',],
			]
			, parent::rules()
		);
	}

}

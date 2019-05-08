<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-04-13
 * Time: 14:34
 */

namespace frontend\models;

use common\models\issue\IssueQuery;
use common\models\issue\IssueSearch as BaseIssueSearch;

class IssueSearch extends BaseIssueSearch {

	public $agents;

	public function search($params) {
		$dataProvider = parent::search($params);
		/** @var IssueQuery $query */
		$query = $dataProvider->query;

		if (!empty($this->agents)) {
			$query->onlyForAgents($this->agents);
		}

		return $dataProvider;
	}
}
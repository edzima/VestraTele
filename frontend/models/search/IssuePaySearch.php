<?php

namespace frontend\models\search;

use common\models\settlement\search\IssuePaySearch as BaseIssuePaySearch;
use yii\db\QueryInterface;

class IssuePaySearch extends BaseIssuePaySearch {

	public string $delay = self::DELAY_ALL;
	public bool $withArchive = false;

	public function applyAgentsFilters(QueryInterface $query): void {
		$query->andWhere(['agent.user_id' => $this->agents_ids]);
		$query->andFilterWhere(['agent.user_id' => $this->agent_id]);
	}

}

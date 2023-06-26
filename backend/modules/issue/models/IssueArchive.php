<?php

namespace backend\modules\issue\models;

use common\models\issue\Issue;
use common\models\issue\query\IssueQuery;

class IssueArchive extends Issue {

	public $count;

	public $max_stage_change_at;

	public static function find(): IssueQuery {
		return parent::find()
			->andWhere('archives_nr IS NOT NULL');
	}
}

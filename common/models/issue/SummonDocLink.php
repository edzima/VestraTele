<?php

namespace common\models\issue;

use common\models\issue\query\SummonQuery;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $summon_id
 * @property int $doc_type_id
 * @property string|null $done_at
 * @property string|null $confirmed_at
 *
 * @property-read Summon $summon
 * @property-read SummonDoc $doc
 */
class SummonDocLink extends ActiveRecord implements IssueInterface {

	use IssueTrait;

	public static function tableName(): string {
		return '{{%summon_doc_list}}';
	}

	public function getSummon(): SummonQuery {
		return $this->hasOne(Summon::class, ['id' => 'summon_id']);
	}

	public function getDoc(): ActiveQuery {
		return $this->hasOne(SummonDoc::class, ['id' => 'doc_type_id']);
	}

	public function getIssueId(): int {
		return $this->summon->issue_id;
	}

	public function getIssueModel(): Issue {
		return $this->summon->issue;
	}

	public function getIssue(): Issue {
		return $this->getIssueModel();
	}
}

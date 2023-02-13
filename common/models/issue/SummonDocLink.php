<?php

namespace common\models\issue;

use common\models\issue\query\SummonDocLinkQuery;
use common\models\issue\query\SummonQuery;
use common\models\user\query\UserQuery;
use common\models\user\User;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $summon_id
 * @property int $doc_type_id
 * @property string|null $done_at
 * @property string|null $confirmed_at
 * @property string|null $deadline_at
 * @property int|null $done_user_id
 * @property int|null $confirmed_user_id
 *
 * @property-read Summon $summon
 * @property-read SummonDoc $doc
 * @property-read User|null $doneUser
 * @property-read User|null $confirmedUser
 */
class SummonDocLink extends ActiveRecord implements IssueInterface {

	use IssueTrait;

	public const STATUS_TO_DO = 'to-do';
	public const STATUS_TO_CONFIRM = 'to-confirm';
	public const STATUS_CONFIRMED = 'confirmed';

	public static function tableName(): string {
		return '{{%summon_doc_list}}';
	}

	public function getSummon(): SummonQuery {
		return $this->hasOne(Summon::class, ['id' => 'summon_id']);
	}

	public function getDoc(): ActiveQuery {
		return $this->hasOne(SummonDoc::class, ['id' => 'doc_type_id']);
	}

	public function getConfirmedUser(): UserQuery {
		return $this->hasOne(User::class, ['id' => 'confirmed_user_id']);
	}

	public function getDoneUser(): SummonQuery {
		return $this->hasOne(User::class, ['id' => 'done_user_id']);
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

	public static function find(): SummonDocLinkQuery {
		return new SummonDocLinkQuery(static::class);
	}
}

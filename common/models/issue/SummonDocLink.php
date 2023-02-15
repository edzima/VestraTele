<?php

namespace common\models\issue;

use common\helpers\Html;
use common\models\issue\query\SummonDocLinkQuery;
use common\models\issue\query\SummonQuery;
use common\models\user\query\UserQuery;
use common\models\user\User;
use Yii;
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

	public function attributeLabels(): array {
		return [
			'confirmedUser' => Yii::t('common', 'Confirmed User'),
			'doneUser' => Yii::t('common', 'Done User'),
			'deadline_at' => Yii::t('common', 'Deadline at'),
			'done_at' => Yii::t('common', 'Done at'),
			'confirmed_at' => Yii::t('common', 'Confirmed at'),
		];
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

	public function getDoneUser(): UserQuery {
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

	public function isDone(): bool {
		return !empty($this->done_at);
	}

	public function isConfirmed(): bool {
		return !empty($this->confirmed_at);
	}

	public function isToDo(): bool {
		return empty($this->done_at);
	}

	public function isToConfirm(): bool {
		return !$this->isToDo() && !$this->isConfirmed();
	}

	public function userNameWithDate(?User $user, ?string $date_at, string $separator = '<br>'): ?string {
		$content = [];
		if ($user) {
			$content[] = Html::encode($user->getFullName());
		}
		if ($date_at) {
			$content[] = Yii::$app->formatter->asDate($date_at);
		}
		if (empty($content)) {
			return null;
		}
		return implode($separator, $content);
	}
}

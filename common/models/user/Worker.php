<?php

namespace common\models\user;

use common\models\hierarchy\ActiveHierarchy;
use common\models\user\query\UserQuery;
use yii\db\ActiveQuery;

/**
 * Class Worker
 *
 * {@inheritdoc}
 *
 * @property-read Worker $parent
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 *
 */
class Worker extends User implements ActiveHierarchy {

	public const ROLES = [
		self::ROLE_AGENT,
		self::ROLE_TELEMARKETER,
		self::ROLE_BOOKKEEPER,
		self::ROLE_CUSTOMER_SERVICE,
		self::ROLE_LAWYER,
		self::ROLE_MANAGER,
	];

	public const PERMISSION_ISSUE_CREATE = 'issue.create';
	public const PERMISSION_ISSUE_DELETE = 'issue.delete';
	public const PERMISSION_ISSUE_LINK_USER = 'issue.link-user';
	public const PERMISSION_ISSUE_CLAIM = 'issue.claim';
	public const PERMISSION_ISSUE_STAGE_CHANGE = 'issue.stage.change';
	public const PERMISSION_ISSUE_STAGE_MANAGER = 'issue.stage.manager';
	public const PERMISSION_ISSUE_TYPE_MANAGER = 'issue.type.manager';

	public const PERMISSION_NOTE_TEMPLATE = 'note.template';
	public const PERMISSION_COST_DEBT = 'cost.debt';
	public const PERMISSION_PROVISION_CHILDREN_VISIBLE = 'provision.children.visible';
	public const PERMISSION_NOTE_DELETE = 'note.delete';
	public const PERMISSION_SUMMON_MANAGER = 'summon.manager';
	public const PERMISSION_SUMMON_CREATE = 'summon.create';

	public const PERMISSION_PAY_ALL_PAID = 'pay.all-paid';
	public const PERMISSION_POTENTIAL_CLIENT = 'potential-client';

	private static $USER_NAMES = [];

	public function getParent(): ActiveQuery {
		return $this->hasOne(static::class, ['id' => 'boss']);
	}

	public function getParentsQuery(): ActiveQuery {
		return static::find()->where(['id' => $this->getParentsIds()]);
	}

	public static function userName(int $id): string {
		return static::userNames()[$id];
	}

	private static function userNames(): array {
		if (empty(static::$USER_NAMES)) {
			static::$USER_NAMES = static::find()
				->select('username')
				->active()
				->asArray()
				->indexBy('id')
				->column();
		}
		return static::$USER_NAMES;
	}

	public static function find(): UserQuery {
		return parent::find()->workers();
	}

}

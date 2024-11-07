<?php

namespace common\models\user;

use backend\modules\settlement\Module as SettlementModule;
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
		self::ROLE_CO_AGENT,
		self::ROLE_TELEMARKETER,
		self::ROLE_BOOKKEEPER,
		self::ROLE_CUSTOMER_SERVICE,
		self::ROLE_LAWYER,
		self::ROLE_LAWYER_ASSISTANT,
		self::ROLE_LAWYER_OFFICE,
		self::ROLE_MANAGER,
		self::ROLE_AUDITOR,
		self::ROLE_AUDITOR_INNER,
		SettlementModule::ROLE_COST_TYPE_MANAGER,
	];
	public const PERMISSION_ENTITY_RESPONSIBLE_MANAGER = 'entity_responsible.manager';

	public const PERMISSION_ISSUE_CLAIM = 'issue.claim';
	public const PERMISSION_ISSUE_CLAIM_TOTAL_SUM = 'issue.claim.totalSum';
	public const PERMISSION_ISSUE_CREATE = 'issue.create';
	public const PERMISSION_ISSUE_DELETE = 'issue.delete';
	public const PERMISSION_ISSUE_LINK_USER = 'issue.link-user';

	public const PERMISSION_WORKERS_WITHOUT_ISSUES = 'workers.without.issues';
	public const PERMISSION_ISSUE_STAGE_CHANGE = 'issue.stage.change';
	public const PERMISSION_ISSUE_STAGE_MANAGER = 'issue.stage.manager';
	public const PERMISSION_ISSUE_TAG_MANAGER = 'issue.tag.manager';
	public const PERMISSION_ISSUE_TYPE_MANAGER = 'issue.type.manager';
	public const PERMISSION_ISSUE_SEARCH_WITH_SETTLEMENTS = 'issue.search.with-settlements';
	public const PERMISSION_ISSUE_STAT = 'issue.stat';
	public const PERMISSION_MESSAGE_EMAIL_ISSUE_CREATE = 'message:email.issue:create';
	public const PERMISSION_MESSAGE_EMAIL_ISSUE_SETTLEMENT_CREATE = 'message:email.issueSettlement:create';
	public const PERMISSION_MESSAGE_EMAIL_ISSUE_PAY_PAID = 'message:email.issuePay:paid';
	public const PERMISSION_MESSAGE_EMAIL_ISSUE_STAGE_CHANGE = 'message:email.issue:stageChange';

	public const PERMISSION_MESSAGE_EMAIL_ISSUE_UPLOAD_FILE = 'message:email.issue:uploadFile';

	public const PERMISSION_ISSUE_NOTE_EMAIL_MESSAGE_ISSUE = 'issue.message.note.issue';

	public const PERMISSION_ISSUE_NOTE_EMAIL_MESSAGE_SUMMON = 'issue.message.note.summon';
	public const PERMISSION_ISSUE_NOTE_EMAIL_MESSAGE_SETTLEMENT = 'issue.message.note.settlement';

	public const PERMISSION_MESSAGE_EMAIL_SUMMON_IMMINENT_DEADLINE = 'message:email.issueSummon:imminentDeadline';
	public const PERMISSION_NOTE_TEMPLATE = 'note.template';
	public const PERMISSION_COST_DEBT = 'cost.debt';

	public const PERMISSION_COST_COMMISSION_REFUND = 'cost.commission_refund';
	public const PERMISSION_PROVISION_CHILDREN_VISIBLE = 'provision.children.visible';
	public const PERMISSION_NOTE_MANAGER = 'note.manager';
	public const PERMISSION_NOTE_DELETE = 'note.delete';
	public const PERMISSION_SUMMON_MANAGER = 'summon.manager';
	public const PERMISSION_SUMMON_CREATE = 'summon.create';
	public const PERMISSION_SUMMON_DOC_MANAGER = 'summon.doc.manager';

	public const PERMISSION_SETTLEMENT_TYPE_MANAGER = 'settlement.type.manager';

	public const PERMISSION_SETTLEMENT_ADMINISTRATIVE_CREATE = 'settlement.administrative.create';
	public const PERMISSION_PAY_ALL_PAID = 'pay.all-paid';
	public const PERMISSION_POTENTIAL_CLIENT = 'potential-client';
	public const PERMISSION_SETTLEMENT_DELETE_NOT_SELF = 'settlement.delete-not-self';
	public const PERMISSION_ISSUE_ATTACHMENTS = 'issue.attachments';
	public const PERMISSION_ISSUE_SEARCH_PARENTS = 'issue.search.parent';
	public const PERMISSION_ISSUE_SHIPMENT = 'issue.shipment';
	public const PERMISSION_CREDIT_ANALYZE = 'credit.analyze';
	public const PERMISSION_COURT = 'court';
	public const PERMISSION_LAWSUIT = 'lawsuit';
	public const PERMISSION_ISSUE_TYPE_PERMISSIONS = 'issue.type.permissions';

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

<?php

namespace backend\modules\issue\models;

use common\models\entityResponsible\EntityResponsible;
use common\models\issue\Issue;
use common\models\issue\IssueStage;
use common\models\issue\IssueTag;
use common\models\issue\IssueTagLink;
use common\models\issue\IssueTagType;
use common\models\issue\IssueType;
use common\models\issue\IssueUser;
use common\models\issue\LinkedIssuesModel;
use common\models\user\User;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class IssueForm
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class IssueForm extends Model implements LinkedIssuesModel {

	public ?int $agent_id = null;
	public ?int $lawyer_id = null;
	public $tele_id;

	public ?int $type_id = null;
	public ?int $stage_id = null;
	public ?int $entity_responsible_id = null;
	public ?string $signing_at = null;
	public ?string $type_additional_date_at = null;
	public ?string $stage_change_at = null;
	public ?string $archives_nr = null;
	public ?string $details = null;
	public ?string $signature_act = null;

	public ?string $stage_deadline_at = null;
	public ?string $entity_agreement_details = null;
	public ?string $entity_agreement_at = null;
	public $linkedIssuesIds;
	public $linkedIssuesAttributes = [];

	public const STAGE_ARCHIVED_ID = IssueStage::ARCHIVES_ID;

	protected const LINKED_ISSUE_ATTRIBUTES_LIST = [
		'details',
		'entity_responsible_id',
		'signature_act',
		'stage_id',
		'stage_deadline_at',
		'type_id',
	];

	public $tagsIds = [];

	private User $customer;

	private ?Issue $model = null;

	private ?array $users = [];

	/**
	 * @inheritdoc
	 */
	public function __construct($config = []) {
		if (!isset($config['customer']) && !isset($config['model'])) {
			throw new InvalidConfigException('$customer or $model must be set.');
		}
		parent::__construct($config);
	}

	public static function getTagsNames(bool $onlyActive): array {
		return IssueTag::getNamesGroupByType($onlyActive);
	}

	public function rules(): array {
		return [
			[['agent_id', 'lawyer_id', 'type_id', 'stage_id', 'entity_responsible_id', 'signing_at'], 'required'],
			[['agent_id', 'lawyer_id', 'tele_id', 'type_id', 'stage_id', 'entity_responsible_id'], 'integer'],
			[['stage_id'], 'filter', 'filter' => 'intval'],
			[
				'stage_id', 'in',
				'when' => function (): bool {
					return !empty($this->type_id);
				},
				'range' => function () {
					return array_keys($this->getStagesData());
				},
				'enableClientValidation' => false,
			],
			[['archives_nr', 'details', 'signature_act', 'entity_agreement_details'], 'string'],
			[['archives_nr', 'details', 'signature_act', 'entity_agreement_details'], 'trim'],
			[['signature_act', 'entity_agreement_details'], 'string', 'max' => 255],
			[
				[
					'signing_at', 'type_additional_date_at', 'stage_change_at', 'stage_deadline_at',
					'entity_agreement_at',
				], 'date', 'format' => 'Y-m-d',
			],
			[['stage_change_at'], 'default', 'value' => date('Y-m-d')],
			[
				[
					'archives_nr', 'details', 'signature_act', 'stage_deadline_at',
					'stage_change_at', 'entity_agreement_details', 'entity_agreement_at',
				], 'default', 'value' => null,
			],
			['type_id', 'in', 'range' => static::getTypesIds()],
			['tagsIds', 'in', 'range' => array_keys(IssueTag::getModels()), 'allowArray' => true],
			['linkedIssuesIds', 'in', 'range' => array_keys($this->getLinkedIssuesNames()), 'allowArray' => true],
			['linkedIssuesAttributes', 'in', 'range' => array_keys($this->getLinkedAttributesNames()), 'allowArray' => true],
			[
				'archives_nr',
				'required',
				'when' => function (): bool {
					return (int) $this->stage_id === IssueStage::ARCHIVES_ID;
				},
				'whenClient' => 'function(attribute, value){
					return isArchived();
				}',
			],
			[['archives_nr'], 'string', 'max' => 10],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return array_merge($this->getModel()->attributeLabels(), [
			'agent_id' => IssueUser::getTypesNames()[IssueUser::TYPE_AGENT],
			'lawyer_id' => IssueUser::getTypesNames()[IssueUser::TYPE_LAWYER],
			'tele_id' => IssueUser::getTypesNames()[IssueUser::TYPE_TELEMARKETER],
			'tagsIds' => Yii::t('issue', 'Tags'),
			'linkedIssuesIds' => Yii::t('issue', 'Linked Issues'),
			'linkedIssuesAttributes' => Yii::t('issue', 'Linked Issues Attributes'),
		]);
	}

	protected function setCustomer(User $customer): void {
		$this->customer = $customer;
	}

	public function getCustomer(): User {
		return $this->customer;
	}

	protected function setModel(Issue $model): void {
		$this->model = $model;
		$this->loadFromModel($model);
	}

	public function loadFromModel(Issue $model, bool $withCustomer = true): void {
		if ($withCustomer) {
			$this->customer = $model->customer;
		}
		$this->type_id = $model->type_id;
		$this->stage_id = $model->stage_id;
		$this->archives_nr = $model->archives_nr;
		$this->signature_act = $model->signature_act;
		$this->agent_id = $model->agent->id;
		$this->lawyer_id = $model->lawyer->id;
		$this->tele_id = $model->tele->id ?? null;
		$this->entity_responsible_id = $model->entity_responsible_id;
		$this->details = $model->details;
		$this->signing_at = $model->signing_at;
		$this->type_additional_date_at = $model->type_additional_date_at;
		$this->stage_change_at = $model->stage_change_at;
		$this->stage_deadline_at = $model->stage_deadline_at;
		$this->entity_agreement_at = $model->entity_agreement_at;
		$this->entity_agreement_details = $model->entity_agreement_details;
		$this->tagsIds = ArrayHelper::getColumn($model->tags, 'id');
	}

	public function setUsers(array $users) {
		$this->users = $users;
		$this->agent_id = $users[IssueUser::TYPE_AGENT] ?? null;
		$this->lawyer_id = $users[IssueUser::TYPE_LAWYER] ?? null;
		$this->tele_id = $users[IssueUser::TYPE_TELEMARKETER] ?? null;
	}

	public function getModel(): Issue {
		if ($this->model === null) {
			$this->model = new Issue();
		}
		return $this->model;
	}

	public function save(): bool {
		if ($this->validate()) {
			$model = $this->getModel();
			$model->type_id = $this->type_id;
			$model->stage_id = $this->stage_id;
			$model->archives_nr = $this->archives_nr;
			$model->signature_act = $this->signature_act;
			$model->details = $this->details;
			$model->stage_change_at = $this->stage_change_at;
			$model->entity_responsible_id = $this->entity_responsible_id;
			$model->signing_at = $this->signing_at;
			$model->type_additional_date_at = $this->type_additional_date_at;
			$model->entity_agreement_at = $this->entity_agreement_at;
			$model->entity_agreement_details = $this->entity_agreement_details;
			$isNewRecord = $model->isNewRecord;
			if ($isNewRecord) {
				$model->generateStageDeadlineAt();
			} else {
				$model->stage_deadline_at = $this->stage_deadline_at;
			}
			if (!$model->save(false)) {
				Yii::warning([
					'message' => 'Issue validate save error.',
					'errors' => $model->getErrors(),
				], __METHOD__);
				return false;
			}

			$this->linkTags(!$isNewRecord);
			$this->linkUsers();
			$this->saveLinkedIssues();

			return true;
		}
		Yii::error($this->getErrors(), __METHOD__);
		return false;
	}

	private function linkUsers(): void {
		$model = $this->getModel();

		if (!empty($this->users)) {
			$data = [];
			foreach ($this->users as $type => $userId) {
				$data[] = [
					'issue_id' => $model->id,
					'type' => $type,
					'user_id' => $userId,
				];
			}
			IssueUser::getDb()
				->createCommand()
				->batchInsert(IssueUser::tableName(), [
					'issue_id',
					'type',
					'user_id',
				], $data)
				->execute();
		} else {
			$model->linkUser($this->customer->id, IssueUser::TYPE_CUSTOMER);
			$model->linkUser($this->agent_id, IssueUser::TYPE_AGENT);
			$model->linkUser($this->lawyer_id, IssueUser::TYPE_LAWYER);
			if (!empty($this->tele_id)) {
				$model->linkUser($this->tele_id, IssueUser::TYPE_TELEMARKETER);
			} else {
				$model->unlinkUser(IssueUser::TYPE_TELEMARKETER);
			}
		}
	}

	private function linkTags(bool $withDelete): void {
		$model = $this->getModel();
		if ($withDelete) {
			IssueTagLink::deleteAll(['issue_id' => $model->id]);
		}

		if (!empty($this->tagsIds)) {
			$rows = [];
			foreach ((array) $this->tagsIds as $id) {
				$rows[] = [
					'issue_id' => $model->id,
					'tag_id' => $id,
				];
			}
			IssueTagLink::getDb()->createCommand()->batchInsert(IssueTagLink::tableName(), [
				'issue_id', 'tag_id',
			], $rows)->execute();
		}
	}

	public static function getAgents(): array {
		return User::getSelectList(
			User::getAssignmentIds([User::ROLE_AGENT, User::PERMISSION_ISSUE])
		);
	}

	public static function getLawyers(): array {
		return User::getSelectList(
			User::getAssignmentIds([User::ROLE_LAWYER, User::PERMISSION_ISSUE])
		);
	}

	public static function getTele(): array {
		return User::getSelectList(
			User::getAssignmentIds([User::ROLE_TELEMARKETER, User::PERMISSION_ISSUE])
		);
	}

	public static function getTypesNames(): array {

		$types = static::getTypesWithStages();
		$names = [];
		foreach ($types as $type) {
			if ($type->parent) {
				$names[$type->parent->name][$type->id] = $type->name;
			} else {
				$names[$type->id] = $type->name;
			}
		}
		foreach ($names as $key => $value) {
			if (is_string($value) && isset($names[$value])) {
				unset($names[$key]);
			}
		}
		return $names;
	}

	public static function getTypesIds(): array {
		return array_keys(static::getTypesWithStages());
	}

	/**
	 * @return IssueType[]
	 */
	protected static function getTypesWithStages(): array {
		$types = [];
		foreach (IssueType::getTypes() as $type) {
			if (
				!empty($type->stages)
				|| ($type->parent !== null && !empty($type->parent->stages))
			) {
				$types[$type->id] = $type;
			}
		}
		return $types;
	}

	public function getStagesData(): array {
		if (empty($this->type_id)) {
			return [];
		}
		return static::getStages($this->type_id);
	}

	public static function getStages(int $typeID): array {
		return IssueStageChangeForm::getStagesNames($typeID);
	}

	public static function getEntityResponsibles(): array {
		return ArrayHelper::map(EntityResponsible::find()->asArray()->all(), 'id', 'name');
	}

	public static function getTypesWithAdditionalDateNames(): array {
		$names = [];
		foreach (IssueType::getTypes() as $type) {
			if ($type->with_additional_date) {
				$names[$type->id] = Yii::t('common', 'Date at ({type})', ['type' => $type->name]);
			}
		}
		return $names;
	}

	public function getLinkedIssuesNames(): array {
		$names = [];
		foreach ($this->getModel()->linkedIssues as $linkedIssue) {
			$names[$linkedIssue->id] = $this->getLinkedIssueName($linkedIssue);
		}
		return $names;
	}

	public function getLinkedIssuesIds(): array {
		if (empty($this->linkedIssuesIds)) {
			return [];
		}
		return (array) $this->linkedIssuesIds;
	}

	public function saveLinkedIssues(): ?int {
		$linkedIssues = $this->getLinkedIssuesIds();
		if (!empty($linkedIssues) && !empty($this->linkedIssuesAttributes)) {
			$attributes = [];
			foreach ($this->linkedIssuesAttributes as $attribute) {
				$attributes[$attribute] = $this->{$attribute};
			}
			return Issue::updateAll($attributes, ['id' => $this->linkedIssuesIds]);
		}
		return null;
	}

	protected function getLinkedIssueName(Issue $issue): string {
		$customerLinkedTags = IssueTagType::linkIssuesGridPositionFilter($issue->getIssueModel()->tags, IssueTagType::LINK_ISSUES_GRID_POSITION_COLUMN_CUSTOMER_BOTTOM);
		if (empty($customerLinkedTags)) {
			return strtr('{customer} - {issue} ({details})', [
				'{customer}' => $issue->getIssueModel()->customer,
				'{details}' => $issue->details,
				'{issue}' => $issue->getIssueName(),
			]);
		}
		$tagsNames = [];
		foreach ($customerLinkedTags as $tag) {
			$tagsNames[] = $tag->name;
		}
		return strtr('{customer} ({tags}) - {issue} ({details})', [
			'{customer}' => $issue->getIssueModel()->customer,
			'{details}' => $issue->details,
			'{issue}' => $issue->getIssueName(),
			'{tags}' => implode(', ', $tagsNames),
		]);
	}

	public function getLinkedAttributesNames(): array {
		$names = [];
		foreach (static::LINKED_ISSUE_ATTRIBUTES_LIST as $attribute) {
			$names[$attribute] = $this->getAttributeLabel($attribute);
		}
		return $names;
	}
}

<?php

namespace backend\modules\issue\models;

use common\models\entityResponsible\EntityResponsible;
use common\models\issue\Issue;
use common\models\issue\IssueStage;
use common\models\issue\IssueTag;
use common\models\issue\IssueTagLink;
use common\models\issue\IssueType;
use common\models\issue\IssueUser;
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
class IssueForm extends Model {

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

	public const STAGE_ARCHIVED_ID = IssueStage::ARCHIVES_ID;

	public $tagsIds = [];

	private User $customer;

	private ?Issue $model = null;

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
		$models = IssueTag::find();
		if ($onlyActive) {
			$models->andWhere(['is_active' => true]);
		}
		$models = $models->all();

		$data = [];
		foreach ($models as $model) {
			$data[$model->getTypeName()][$model->id] = $model->name;
		}
		return $data;
	}

	public function rules(): array {
		return [
			[['agent_id', 'lawyer_id', 'type_id', 'stage_id', 'entity_responsible_id', 'signing_at'], 'required'],
			[['agent_id', 'lawyer_id', 'tele_id', 'type_id', 'stage_id', 'entity_responsible_id'], 'integer'],
			[['stage_id'], 'filter', 'filter' => 'intval'],
			[
				'stage_id', 'in', 'when' => function (): bool {
				return !empty($this->type_id);
			}, 'range' => function () {
				return array_keys($this->getStagesData());
			}, 'enableClientValidation' => false,
			],
			[['details', 'signature_act'], 'string'],
			['signature_act', 'string', 'max' => 30],
			[['signing_at', 'type_additional_date_at', 'stage_change_at', 'stage_deadline_at'], 'date', 'format' => 'Y-m-d'],
			[['stage_change_at'], 'default', 'value' => date('Y-m-d')],
			[['signature_act', 'stage_deadline_at', 'stage_change_at'], 'default', 'value' => null],
			['type_id', 'in', 'range' => static::getTypesIds()],
			['tagsIds', 'in', 'range' => IssueTag::find()->select('id')->column(), 'allowArray' => true],
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
		$this->type_id = $model->type_id;
		$this->stage_id = $model->stage_id;
		$this->archives_nr = $model->archives_nr;
		$this->signature_act = $model->signature_act;
		$this->agent_id = $model->agent->id;
		$this->lawyer_id = $model->lawyer->id;
		$this->tele_id = $model->tele->id ?? null;
		$this->customer = $model->customer;
		$this->entity_responsible_id = $model->entity_responsible_id;
		$this->details = $model->details;
		$this->signing_at = $model->signing_at;
		$this->type_additional_date_at = $model->type_additional_date_at;
		$this->stage_change_at = $model->stage_change_at;
		$this->stage_deadline_at = $model->stage_deadline_at;
		$this->tagsIds = ArrayHelper::getColumn($model->tags, 'id');
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
			if ($model->isNewRecord) {
				$model->generateStageDeadlineAt();
			} else {
				$model->stage_deadline_at = $this->stage_deadline_at;
			}
			if (!$model->save(false)) {
				return false;
			}

			$this->linkTags();
			$this->linkUsers();

			return true;
		}
		Yii::error($this->getModel()->getErrors(), 'issueForm');
		return false;
	}

	private function linkUsers(): void {
		$model = $this->getModel();
		$model->linkUser($this->customer->id, IssueUser::TYPE_CUSTOMER);
		$model->linkUser($this->agent_id, IssueUser::TYPE_AGENT);
		$model->linkUser($this->lawyer_id, IssueUser::TYPE_LAWYER);
		if (!empty($this->tele_id)) {
			$model->linkUser($this->tele_id, IssueUser::TYPE_TELEMARKETER);
		} else {
			$model->unlinkUser(IssueUser::TYPE_TELEMARKETER);
		}
	}

	private function linkTags(): void {
		$model = $this->getModel();
		if (!$model->isNewRecord) {
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
		$parents = IssueType::getParents();
		if (empty($parents)) {
			return IssueType::getTypesNames();
		}
		$names = [];
		foreach ($parents as $parent) {
			$names[$parent->name] = ArrayHelper::map($parent->childs, 'id', 'name');
		}
		return $names;
	}

	public static function getTypesIds(): array {
		$parents = IssueType::getParents();
		if (empty($parents)) {
			return array_keys(IssueType::getTypesNames());
		}
		$ids = [];
		foreach ($parents as $parent) {
			foreach ($parent->childs as $child) {
				$ids[] = $child->id;
			}
		}
		return $ids;
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

}

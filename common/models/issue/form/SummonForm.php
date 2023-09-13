<?php

namespace common\models\issue\form;

use common\models\entityResponsible\EntityResponsible;
use common\models\forms\HiddenFieldsModel;
use common\models\issue\Issue;
use common\models\issue\IssueInterface;
use common\models\issue\IssueUser;
use common\models\issue\Summon;
use common\models\issue\SummonDoc;
use common\models\issue\SummonDocLink;
use common\models\issue\SummonType;
use common\models\SummonTypeOptions;
use common\models\user\User;
use common\models\user\Worker;
use DateTime;
use edzima\teryt\models\Simc;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Form model for Summon Model.
 *
 * @see Summon
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class SummonForm extends Model implements HiddenFieldsModel {

	public const SCENARIO_CONTRACTOR = 'contractor';

	public const TERM_EMPTY = 'empty';
	public const TERM_ONE_DAY = 1;
	public const TERM_TREE_DAYS = 3;
	public const TERM_FIVE_DAYS = 5;
	public const TERM_ONE_WEEK = 7;
	public const TERM_TEN_DAYS = 10;
	public const TERM_TWO_WEEKS = 14;
	public const TERM_THREE_WEEKS = 21;

	public const TERM_FOUR_WEEKS = 28;

	public const TERM_ONE_MONTH = 30;
	public const TERM_TWO_MONTH = 61;
	public const TERM_CUSTOM = 'custom';

	public int $owner_id;

	public int $status = Summon::STATUS_NEW;
	public ?int $type_id = null;
	public $term = self::TERM_ONE_WEEK;
	public string $title = '';
	public ?int $issue_id = null;
	public ?int $contractor_id = null;
	public ?int $entity_id = null;
	public ?int $city_id = null;

	public $doc_types_ids = [];

	public $start_at;
	public $deadline_at;
	public $realize_at;
	public $realized_at;

	public ?int $updater_id = null;

	public bool $sendEmailToContractor = true;

	private ?Summon $model = null;
	private ?array $_contractorIds = null;
	private ?SummonType $type = null;
	private ?IssueInterface $_issue = null;

	public function rules(): array {
		return [
			[['type_id', 'status', 'issue_id', 'owner_id', 'start_at'], 'required'],
			[$this->requiredFields(), 'required'],
			[['type_id', 'issue_id', 'owner_id', 'contractor_id', 'status', 'entity_id', '!updater_id'], 'integer'],
			[
				'title', 'required',
				'message' => Yii::t('issue', 'Title cannot be blank when Docs are empty.'),
				'when' => function (): bool {
					return $this->getType() === null && empty($this->doc_types_ids);
				},
				'enableClientValidation' => false,
			],
			[
				'doc_types_ids', 'required',
				'message' => Yii::t('issue', 'Docs cannot be blank when Title is empty.'),
				'when' => function (): bool {
					return $this->getType() === null && empty($this->title);
				},
				'enableClientValidation' => false,
			],
			['sendEmailToContractor', 'boolean'],
			[['title'], 'string', 'max' => 255],
			[['start_at', 'realize_at', 'realized_at'], 'safe'],
			[
				'deadline_at', 'required', 'enableClientValidation' => false, 'when' => function () {
				return $this->getModel()->isNewRecord && $this->term === static::TERM_CUSTOM;
			},
				'message' => Yii::t('common', 'Deadline At cannot be blank on custom term.'),
			],
			[['deadline_at', 'start_at'], 'default', 'value' => null],
			[['start_at', 'deadline_at'], 'date', 'format' => 'yyyy-MM-dd'],
			[['realize_at', 'realized_at'], 'datetime', 'format' => 'yyyy-MM-dd HH:mm:00'],
			['entity_id', 'in', 'range' => array_keys(static::getEntityNames())],
			['status', 'in', 'range' => array_keys(static::getStatusesNames())],
			['type_id', 'in', 'range' => array_keys(static::getTypesNames())],
			['doc_types_ids', 'in', 'range' => array_keys($this->getDocNames()), 'allowArray' => true],
			['term', 'in', 'range' => array_keys(static::getTermsNames())],
			[['contractor_id'], 'in', 'range' => array_keys($this->getContractors()),],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
			[['!owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['owner_id' => 'id']],
			[['!updater_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updater_id' => 'id']],
		];
	}

	protected function requiredFields(): array {
		if ($this->getType() === null || empty($this->getType()->getOptions()->requiredFields)) {
			return [
				'city_id',
				'entity_id',
				'contractor_id',
			];
		}
		return $this->getType()->getOptions()->requiredFields;
	}

	public function scenarios(): array {
		$scenarios = parent::scenarios();
		$scenarios[static::SCENARIO_CONTRACTOR] = [
			'status',
			'realize_at',
			'realized_at',
		];
		return $scenarios;
	}

	public function getCity(): ?Simc {
		return $this->getModel()->city;
	}

	public function attributeLabels(): array {
		return array_merge(
			$this->getModel()->attributeLabels(), [
			'term' => Yii::t('common', 'Term'),
			'sendEmailToContractor' => Yii::t('issue', 'Send Email To Contractor'),
			'start_at' => Yii::t('issue', 'Date At'),
			'deadline_at' => Yii::t('common', 'Deadline At(Day)'),
			'updater_id' => Yii::t('common', 'Updater'),
		]);
	}

	public function getIssue(): ?IssueInterface {
		if ($this->_issue === null || $this->_issue->getId() !== $this->issue_id) {
			$this->_issue = Issue::findOne($this->issue_id);
		}
		return $this->_issue;
	}

	public function getModel(): Summon {
		if ($this->model === null) {
			$this->model = new Summon();
		}
		return $this->model;
	}

	public function setType(SummonType $type): void {
		$this->type = $type;
		$this->type_id = $type->id;
		$this->setOptions($type->getOptions());
	}

	public function setOptions(SummonTypeOptions $options): void {
		if ($options->status) {
			$this->status = $options->status;
		}
		if ($options->title) {
			$this->title = $options->title;
		}
		if ($options->sendEmailToContractor !== null) {
			$this->sendEmailToContractor = $options->sendEmailToContractor;
		}
		$this->term = $options->term;
	}

	public function setModel(Summon $model): void {
		$this->model = $model;
		$this->status = $model->status;
		$this->type_id = $model->type_id;
		$this->doc_types_ids = ArrayHelper::getColumn($model->docs, 'id');
		$this->issue_id = $model->issue_id;
		$this->title = $model->title;
		$this->contractor_id = $model->contractor_id;
		$this->owner_id = $model->owner_id;
		$this->entity_id = $model->entity_id;
		$this->city_id = $model->city_id;
		$this->start_at = $model->start_at ? date('Y-m-d', strtotime($model->start_at)) : null;
		$this->deadline_at = $model->deadline_at ? date('Y-m-d', strtotime($model->deadline_at)) : null;
		$this->realize_at = $model->realize_at;
		$this->realized_at = $model->realized_at;
	}

	public function save(): bool {
		if (!$this->validate()) {
			Yii::warning($this->getErrors(), 'summonForm.validate');
			return false;
		}
		$model = $this->getModel();
		$model->status = $this->status;
		$model->type_id = $this->type_id;

		$model->issue_id = $this->issue_id;
		$model->title = $this->title;
		$model->contractor_id = $this->contractor_id;
		$model->owner_id = $this->owner_id;
		$model->start_at = $this->start_at;
		$model->city_id = $this->city_id;
		$model->entity_id = $this->entity_id;
		$model->updater_id = $this->updater_id;
		if (empty($this->realize_at)) {
			$dateTime = new DateTime($this->start_at);
			$dateTime->setTime(date('H'), date('i'));
			$this->realize_at = $dateTime->format(DATE_ATOM);
		}
		$model->realize_at = $this->realize_at;

		if ($model->isNewRecord && $this->term !== static::TERM_CUSTOM) {
			if ($this->term === static::TERM_EMPTY) {
				$this->deadline_at = null;
			} else {
				$this->deadline_at = date('Y-m-d', strtotime($this->start_at . " + {$this->term} days"));
			}
		}

		$model->deadline_at = $this->deadline_at;
		if (empty($this->realized_at) && $this->status === Summon::STATUS_REALIZED) {
			$this->realized_at = date(DATE_ATOM);
		}
		$model->realized_at = $this->realized_at;
		if ($model->save()) {
			$this->saveDocs();
			return true;
		}
		Yii::warning($model->getErrors(), 'summonForm.save');
		$this->addErrors($model->getErrors());
		return false;
	}

	public function saveDocs(): void {
		$model = $this->getModel();
		if (empty($this->doc_types_ids)) {
			if (!$model->isNewRecord) {
				$model->unlinkAll('docs', true);
			}
		} else {
			if ($model->isNewRecord) {
				$this->linkDocsTypes($this->doc_types_ids);
			} else {
				$currentTypesIds = ArrayHelper::getColumn($model->docsLink, 'doc_type_id');
				if (empty($currentTypesIds)) {
					$this->linkDocsTypes($this->doc_types_ids);
				} else {
					$toDeleteIds = array_diff($currentTypesIds, $this->doc_types_ids);
					$toInsertIds = array_diff($this->doc_types_ids, $currentTypesIds);
					if (!empty($toDeleteIds)) {
						$this->unlinkDocsTypes($toDeleteIds);
					}
					if (!empty($toInsertIds)) {
						$this->linkDocsTypes($toInsertIds);
					}
				}
				if ((int) $this->status === Summon::STATUS_REALIZED) {
					SummonDocLink::updateAll([
						'confirmed_at' => date(DATE_ATOM),
						'confirmed_user_id' => $this->updater_id ?: $this->owner_id,
					], [
						'summon_id' => $model->id,
						'confirmed_user_id' => null,
					]);
					SummonDocLink::updateAll([
						'done_at' => date(DATE_ATOM),
						'done_user_id' => $this->updater_id ?: $this->owner_id,
					], [
						'summon_id' => $model->id,
						'done_at' => null,
					]);
				}
			}
		}
	}

	public function sendEmailToContractor(): bool {
		if (!$this->sendEmailToContractor || empty($this->getModel()->contractor->email)) {
			return false;
		}
		$model = $this->getModel();
		return Yii::$app->mailer
			->compose(
				['html' => 'summonCreate-html',],
				['model' => $model]
			)
			->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name . ' robot'])
			->setTo($model->contractor->email)
			->setSubject(Yii::t('issue', 'You have new Summon: {type}', ['type' => $model->getTypeName()]))
			->send();
	}

	public function getDocNames(): array {
		if (empty($this->type_id)) {
			return SummonDoc::getNames();
		}
		return SummonDoc::getNames($this->type_id);
	}

	public static function getStatusesNames(): array {
		return Summon::getStatusesNames();
	}

	public static function getTypesNames(): array {
		return SummonType::getNames();
	}

	public static function getEntityNames(): array {
		return ArrayHelper::map(
			EntityResponsible::find()->summons()->all(),
			'id',
			'name'
		);
	}

	public function getContractors(): array {
		if ($this->_contractorIds === null) {
			$ids = User::getAssignmentIds([User::PERMISSION_SUMMON]);
			if ($this->issue_id) {
				$issueUsersIds = IssueUser::find()
					->select('user_id')
					->andWhere(['issue_id' => $this->issue_id])
					->column();
				$ids = array_merge($ids, $issueUsersIds);
			}
			$this->_contractorIds = User::getSelectList($ids);
		}
		return $this->_contractorIds;
	}

	public static function getTermsNames(): array {
		return [
			static::TERM_ONE_DAY => Yii::t('common', '1 Day'),
			static::TERM_TREE_DAYS => Yii::t('common', '1 Days'),
			static::TERM_FIVE_DAYS => Yii::t('common', '5 Days'),
			static::TERM_ONE_WEEK => Yii::t('common', 'Week'),
			static::TERM_TEN_DAYS => Yii::t('common', '10 Days'),
			static::TERM_TWO_WEEKS => Yii::t('common', '2 Weeks'),
			static::TERM_THREE_WEEKS => Yii::t('common', '3 Weeks'),
			static::TERM_FOUR_WEEKS => Yii::t('common', '4 Weeks'),
			static::TERM_ONE_MONTH => Yii::t('common', 'One Month'),
			static::TERM_TWO_MONTH => Yii::t('common', 'Two Months'),
			static::TERM_EMPTY => Yii::t('common', 'Without Term'),
			static::TERM_CUSTOM => Yii::t('common', 'Custom Term'),
		];
	}

	public function isVisibleField(string $attribute): bool {
		if (!array_key_exists($attribute, $this->attributes)) {
			return true;
		}
		if ($this->getType() === null) {
			return true;
		}
		if ($attribute === 'type_id') {
			return false;
		}
		if ($attribute === 'status') {
			return true;
		}
		if ($attribute === 'realized_at' && $this->getModel()->isRealized()) {
			return true;
		}
		return $this->getType()->isForFormAttribute($attribute);
	}

	public function getType(): ?SummonType {
		if ($this->type === null && !$this->getModel()->isNewRecord) {
			return $this->getModel()->type;
		}
		return $this->type;
	}

	private function linkDocsTypes(array $doc_types_ids) {
		$summonId = $this->getModel()->id;
		if ($summonId) {
			$rows = [];
			foreach ($doc_types_ids as $doc_type_id) {
				$rows[] = [
					'doc_type_id' => $doc_type_id,
					'summon_id' => $summonId,
				];
			}
			if (!empty($rows)) {
				Yii::$app->db->createCommand()
					->batchInsert(SummonDocLink::tableName(), [
						'doc_type_id',
						'summon_id',
					], $rows)
					->execute();
			}
		}
	}

	private function unlinkDocsTypes(array $doc_types_ids) {
		$summonId = $this->getModel()->id;
		if ($summonId && !empty($doc_types_ids)) {
			SummonDocLink::deleteAll([
				'summon_id' => $summonId,
				'doc_type_id' => $doc_types_ids,
			]);
		}
	}
}

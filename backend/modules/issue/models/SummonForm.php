<?php

namespace backend\modules\issue\models;

use common\models\entityResponsible\EntityResponsible;
use common\models\issue\Issue;
use common\models\issue\IssueUser;
use common\models\issue\Summon;
use common\models\user\User;
use common\models\user\Worker;
use edzima\teryt\models\Simc;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Form model for summon in backend app.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class SummonForm extends Model {

	public const TERM_EMPTY = null;
	public const TERM_ONE_DAY = 1;
	public const TERM_TREE_DAYS = 3;
	public const TERM_FIVE_DAYS = 5;
	public const TERM_ONE_WEEK = 7;
	public const TERM_TWO_WEEKS = 14;
	public const TERM_THREE_WEEKS = 21;
	public const TERM_ONE_MONTH = 30;
	public const TERM_CUSTOM = 'custom';

	public int $owner_id;

	public int $status = Summon::STATUS_NEW;
	public int $type = Summon::TYPE_APPEAL;
	public $term = self::TERM_ONE_WEEK;
	public string $title = '';
	public ?int $issue_id = null;
	public ?int $contractor_id = null;
	public ?int $entity_id = null;
	public ?int $city_id = null;

	public $start_at;
	public $deadline_at;
	public $realize_at;
	public $realized_at;

	private ?Summon $model = null;

	private ?array $_contractorIds = null;

	public function rules(): array {
		return [
			[['type', 'status', 'title', 'issue_id', 'owner_id', 'contractor_id', 'start_at', 'entity_id', 'city_id'], 'required'],
			[['type', 'issue_id', 'owner_id', 'contractor_id', 'status'], 'integer'],
			[['start_at', 'realize_at', 'realized_at'], 'safe'],
			[['start_at', 'deadline_at'], 'date', 'format' => 'yyyy-MM-dd'],
			[['realize_at', 'realized_at'], 'date', 'format' => 'yyyy-MM-dd HH:mm'],
			['status', 'in', 'range' => array_keys(static::getStatusesNames())],
			['type', 'in', 'range' => array_keys(static::getTypesNames())],
			['term', 'in', 'range' => array_keys(static::getTermsNames())],
			[['title'], 'string', 'max' => 255],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
			[['contractor_id'], 'in', 'range' => array_keys($this->getContractors()),],
			[['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['owner_id' => 'id']],
		];
	}

	public function getCity(): ?Simc {
		return $this->getModel()->city;
	}

	public function attributeLabels(): array {
		return array_merge(
			$this->getModel()->attributeLabels(), [
			'term' => Yii::t('common', 'Term'),
		]);
	}

	public function getModel(): Summon {
		if ($this->model === null) {
			$this->model = new Summon();
		}
		return $this->model;
	}

	public function setModel(Summon $model): void {
		$this->model = $model;
		$this->status = $model->status;
		$this->type = $model->type;
		$this->issue_id = $model->issue_id;
		$this->title = $model->title;
		$this->contractor_id = $model->contractor_id;
		$this->owner_id = $model->owner_id;
		$this->entity_id = $model->entity_id;
		$this->city_id = $model->city_id;
		$this->start_at = $model->start_at;
		$this->deadline_at = $model->deadline_at;
		$this->realize_at = $model->realize_at;
		$this->realized_at = $model->realized_at;
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->status = $this->status;
		$model->type = $this->type;
		$model->issue_id = $this->issue_id;
		$model->title = $this->title;
		$model->contractor_id = $this->contractor_id;
		$model->owner_id = $this->owner_id;
		$model->start_at = $this->start_at;
		$model->city_id = $this->city_id;
		$model->entity_id = $this->entity_id;
		$model->realize_at = $this->realize_at;
		$model->realized_at = $this->realized_at;
		if ($model->isNewRecord && $this->term !== static::TERM_CUSTOM) {
			if ($this->term === static::TERM_EMPTY) {
				$this->deadline_at = null;
			} else {
				$this->deadline_at = date('Y-m-d', strtotime($this->start_at . " + {$this->term} days"));
			}
		}

		$model->deadline_at = $this->deadline_at;
		if ($model->save()) {
			return true;
		}
		Yii::warning('summon.errors', [
			'attributes' => $model->getAttributes(),
			'errors' => $model->getErrors(),
		]);
		$this->addErrors($model->getErrors());
		return false;
	}

	public static function getStatusesNames(): array {
		return Summon::getStatusesNames();
	}

	public static function getTypesNames(): array {
		return Summon::getTypesNames();
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
			$ids = Worker::getAssignmentIds([Worker::PERMISSION_SUMMON]);
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

	//@todo add I18n
	public static function getTermsNames(): array {
		return [
			static::TERM_ONE_DAY => '1 dzień',
			static::TERM_TREE_DAYS => '3 dni',
			static::TERM_FIVE_DAYS => '5 dni',
			static::TERM_ONE_WEEK => 'Tydzień',
			static::TERM_TWO_WEEKS => '2 tygodnie',
			static::TERM_THREE_WEEKS => '3 tygodnie ',
			static::TERM_ONE_MONTH => 'Miesiąc',
			static::TERM_EMPTY => 'Bez terminu',
			static::TERM_CUSTOM => 'Custom',
		];
	}

}

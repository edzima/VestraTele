<?php

namespace backend\modules\issue\models;

use common\models\entityResponsible\EntityResponsible;
use common\models\issue\Issue;
use common\models\issue\IssueUser;
use common\models\issue\Summon;
use common\models\user\User;
use common\models\user\Worker;
use edzima\teryt\models\Simc;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Form model for summon in backend app.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class SummonForm extends Model {

	public int $owner_id;

	public int $status = Summon::STATUS_NEW;
	public int $type = Summon::TYPE_DOCUMENTS;
	public $term = Summon::TERM_ONE_WEEK;
	public string $title = '';
	public ?int $issue_id = null;
	public ?int $contractor_id = null;
	public ?int $entity_id = null;
	public ?int $city_id = null;

	public $start_at;
	public $realize_at;
	public $realized_at;

	private ?Summon $model = null;

	private ?array $_contractorIds = null;

	public function rules(): array {
		return [
			[['type', 'status', 'title', 'issue_id', 'owner_id', 'contractor_id', 'start_at', 'entity_id', 'city_id'], 'required'],
			[['type', 'issue_id', 'owner_id', 'contractor_id', 'status'], 'integer'],
			[['start_at', 'realize_at', 'realized_at'], 'safe'],
			['start_at', 'date', 'format' => 'yyyy-MM-dd'],
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
		return $this->getModel()->attributeLabels();
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
		$this->term = $model->term;
		$this->issue_id = $model->issue_id;
		$this->title = $model->title;
		$this->contractor_id = $model->contractor_id;
		$this->owner_id = $model->owner_id;
		$this->entity_id = $model->entity_id;
		$this->city_id = $model->city_id;
		$this->start_at = $model->start_at;
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
		$model->term = $this->term;
		$model->issue_id = $this->issue_id;
		$model->title = $this->title;
		$model->contractor_id = $this->contractor_id;
		$model->owner_id = $this->owner_id;
		$model->start_at = $this->start_at;
		$model->city_id = $this->city_id;
		$model->entity_id = $this->entity_id;
		$model->realize_at = $this->realize_at;
		$model->realized_at = $this->realized_at;
		if ($model->save()) {
			return true;
		}
		$this->addErrors($model->getErrors());
		return false;
	}

	public static function getStatusesNames(): array {
		return Summon::getStatusesNames();
	}

	public static function getTypesNames(): array {
		return Summon::getTypesNames();
	}

	public static function getTermsNames(): array {
		return Summon::getTermsNames();
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

}

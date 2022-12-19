<?php

namespace backend\modules\issue\models;

use common\models\issue\IssueStageType;
use common\models\issue\IssueType;
use Yii;
use yii\base\Model;

class StageTypeForm extends Model {

	public $type_id;
	public $stage_id;
	public $calendar_background;
	public $days_reminder;

	private ?IssueStageType $model = null;

	public function rules(): array {
		return [
			[['type_id', 'stage_id'], 'required'],
			[['type_id', 'stage_id', 'days_reminder'], 'integer'],
			[['calendar_background'], 'string'],
			[['days_reminder', 'calendar_background'], 'default', 'value' => null],
			['type_id', 'in', 'range' => array_keys($this->getTypesNames())],
			['stage_id', 'in', 'range' => array_keys($this->getStagesNames())],
		];
	}

	public function getTypesNames(): array {
		return IssueType::getTypesNames();
	}

	public function getStagesNames(): array {
		return IssueStage::getStagesNames(true);
	}

	public function attributeLabels(): array {
		return [
			'stage_id' => Yii::t('issue', 'Stage'),
			'type_id' => Yii::t('issue', 'Type'),
			'days_reminder' => Yii::t('common', 'Reminder (days)'),
			'calendar_background' => Yii::t('common', 'Calendar Background'),
		];
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->stage_id = $this->stage_id;
		$model->type_id = $this->type_id;
		$model->calendar_background = $this->calendar_background;
		$model->days_reminder = $this->days_reminder;
		Yii::$app->db->createCommand()
			->upsert(IssueStageType::tableName(), [
				'stage_id' => $this->stage_id,
				'type_id' => $this->type_id,
				'calendar_background' => $this->calendar_background,
				'days_reminder' => $this->days_reminder,
			])->execute();
		return true;
	}

	public function getModel(): IssueStageType {
		if ($this->model === null) {
			$this->model = new IssueStageType();
		}
		return $this->model;
	}

	public function setModel(IssueStageType $model): void {
		$this->model = $model;
		$this->type_id = $model->type_id;
		$this->stage_id = $model->stage_id;
		$this->calendar_background = $model->calendar_background;
		$this->days_reminder = $model->days_reminder;
	}

}

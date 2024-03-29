<?php

namespace backend\modules\issue\models;

use common\models\issue\Issue;
use common\models\issue\IssueStageType;
use common\models\issue\IssueType;
use Yii;
use yii\base\Model;
use yii\db\Expression;

class StageTypeForm extends Model {

	public const SCENARIO_CREATE = 'create';

	public $type_id;
	public $stage_id;
	public $calendar_background;
	public $days_reminder;

	public $updateIssuesStageDeadlineAt = false;

	private ?IssueStageType $model = null;

	public function rules(): array {
		return [
			[['type_id', 'stage_id', 'updateIssuesStageDeadlineAt'], 'required', 'on' => [static::SCENARIO_CREATE, static::SCENARIO_DEFAULT]],
			['updateIssuesStageDeadlineAt', 'boolean'],
			[['type_id', 'stage_id', 'days_reminder'], 'integer'],
			[['calendar_background'], 'string'],
			[['days_reminder', 'calendar_background'], 'default', 'value' => null],
			['type_id', 'in', 'range' => array_keys($this->getTypesNames())],
			['stage_id', 'in', 'range' => array_keys($this->getStagesNames())],
		];
	}

	public function getTypesNames(): array {
		$names = IssueType::getTypesNames();
		if ($this->scenario === static::SCENARIO_CREATE && $this->stage_id !== null) {
			$stage = IssueStage::get($this->stage_id);
			if ($stage !== null) {
				foreach ($names as $id => $name) {
					if ($stage->hasType($id)) {
						unset($names[$id]);
					}
				}
			}
		}
		return $names;
	}

	public function getStagesNames(): array {
		$names = IssueStage::getStagesNames(true);
		if ($this->scenario === static::SCENARIO_CREATE && $this->type_id !== null) {
			$type = IssueType::get($this->type_id);
			if ($type !== null) {
				foreach ($names as $id => $name) {
					if ($type->hasStage($id)) {
						unset($names[$id]);
					}
				}
			}
		}
		return $names;
	}

	public function attributeLabels(): array {
		return [
			'stage_id' => Yii::t('issue', 'Stage'),
			'type_id' => Yii::t('issue', 'Type'),
			'days_reminder' => Yii::t('common', 'Reminder (days)'),
			'calendar_background' => Yii::t('common', 'Calendar Background'),
			'updateIssuesStageDeadlineAt' => Yii::t('issue', 'Update Issues Stage Deadline at'),
		];
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		$this->upsert();
		if ($this->updateIssuesStageDeadlineAt) {
			$this->updateIssuesStageDeadlineAt();
		}
		return true;
	}

	private function upsert(): bool {
		return Yii::$app->db->createCommand()
			->upsert(IssueStageType::tableName(), [
				'stage_id' => $this->stage_id,
				'type_id' => $this->type_id,
				'calendar_background' => $this->calendar_background,
				'days_reminder' => $this->days_reminder,
			])->execute();
	}

	public function getType(): ?IssueType {
		if ($this->type_id) {
			return IssueType::get($this->type_id);
		}
		return null;
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

	private function updateIssuesStageDeadlineAt(): ?int {
		if ($this->stage_id === null) {
			return null;
		}
		$type = $this->getType();
		if ($type === null) {
			return null;
		}

		$typesIds = [];

		$typesIds[$type->id] = $type->id;
		foreach ($type->childs as $child) {
			$typesIds[$child->id] = $child->id;
		}
		$count = 0;
		$days = (int) $this->days_reminder;
		$stageId = $this->stage_id;
		if ($days) {
			$count += Issue::updateAll([
				'stage_deadline_at' => new Expression("DATE_ADD(stage_change_at, INTERVAL $days DAY)"),
			],
				[
					'AND',
					['IS NOT', 'stage_change_at', null],
					['stage_id' => $stageId],
					['type_id' => $typesIds],
				]
			);
			$count += Issue::updateAll([
				'stage_deadline_at' => new Expression("DATE_ADD(created_at, INTERVAL $days DAY)"),
			],
				[
					'stage_id' => $stageId,
					'type_id' => $typesIds,
					'stage_change_at' => null,
				]
			);
		} else {
			$count += Issue::updateAll([
				'stage_deadline_at' => null,
			],
				[
					'stage_id' => $stageId,
					'type_id' => $typesIds,
				]
			);
		}
		return $count;
	}

}

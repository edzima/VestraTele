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
	public $days_reminder_second;
	public $days_reminder_third;
	public $days_reminder_fourth;
	public $days_reminder_fifth;

	private ?IssueStageType $model = null;

	public function rules(): array {
		return [
			[['type_id', 'stage_id'], 'required', 'on' => [static::SCENARIO_CREATE, static::SCENARIO_DEFAULT]],
			[['type_id', 'stage_id', 'days_reminder', 'days_reminder_second', 'days_reminder_third', 'days_reminder_fourth', 'days_reminder_fifth'], 'integer'],
			[['calendar_background'], 'string'],
			[['days_reminder', 'calendar_background', 'days_reminder_second', 'days_reminder_third', 'days_reminder_fourth', 'days_reminder_fifth'], 'default', 'value' => null],
			['days_reminder_second', 'compare', 'operator' => '>', 'compareAttribute' => 'days_reminder', 'enableClientValidation' => false],
			['days_reminder_third', 'compare', 'operator' => '>', 'compareAttribute' => 'days_reminder_second', 'enableClientValidation' => false],
			['days_reminder_fourth', 'compare', 'operator' => '>', 'compareAttribute' => 'days_reminder_third', 'enableClientValidation' => false],
			['days_reminder_fifth', 'compare', 'operator' => '>', 'compareAttribute' => 'days_reminder_fourth', 'enableClientValidation' => false],
			[
				'days_reminder', 'compare', 'operator' => '<', 'compareAttribute' => 'days_reminder_second', 'enableClientValidation' => false, 'when' => function (): bool {
				return !empty($this->days_reminder_second);
			},
			],
			[
				'days_reminder_second', 'compare', 'operator' => '<', 'compareAttribute' => 'days_reminder_third', 'enableClientValidation' => false, 'when' => function (): bool {
				return !empty($this->days_reminder_third);
			},
			],
			[
				'days_reminder_third', 'compare', 'operator' => '<', 'compareAttribute' => 'days_reminder_fourth', 'enableClientValidation' => false, 'when' => function (): bool {
				return !empty($this->days_reminder_fourth);
			},
			],
			[
				'days_reminder_fourth', 'compare', 'operator' => '<', 'compareAttribute' => 'days_reminder_fifth', 'enableClientValidation' => false, 'when' => function (): bool {
				return !empty($this->days_reminder_fifth);
			},
			],
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
		return IssueStageType::instance()->attributeLabels();
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		$this->upsert();
		$this->updateIssuesStageDeadlineAt();
		return true;
	}

	private function upsert(): bool {
		return Yii::$app->db->createCommand()
			->upsert(IssueStageType::tableName(), [
				'stage_id' => $this->stage_id,
				'type_id' => $this->type_id,
				'calendar_background' => $this->calendar_background,
				'days_reminder' => $this->days_reminder,
				'days_reminder_second' => $this->days_reminder_second,
				'days_reminder_third' => $this->days_reminder_third,
				'days_reminder_fourth' => $this->days_reminder_fourth,
				'days_reminder_fifth' => $this->days_reminder_fifth,
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
		$this->days_reminder_second = $model->days_reminder_second;
		$this->days_reminder_third = $model->days_reminder_third;
		$this->days_reminder_fourth = $model->days_reminder_fourth;
		$this->days_reminder_fifth = $model->days_reminder_fifth;
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

<?php

namespace backend\modules\issue\models;

use common\models\issue\Issue;
use common\models\issue\IssueType;
use Yii;
use yii\base\Model;
use yii\db\Expression;
use yii\db\QueryInterface;

class IssueStageForm extends Model {

	public string $name = '';
	public string $short_name = '';
	public $posi;
	public array $typesIds = [];
	public $days_reminder;
	public ?string $calendar_background = null;

	private ?IssueStage $model = null;

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['name', 'short_name', 'typesIds'], 'required'],
			[['posi', 'days_reminder'], 'integer'],
			['posi', 'default', 'value' => 0],
			['calendar_background', 'default', 'value' => null],
			['days_reminder', 'integer', 'min' => 1, 'max' => 365],
			['days_reminder', 'default', 'value' => null],
			[['name', 'short_name', 'calendar_background'], 'string', 'max' => 255],
			[
				['name', 'short_name'],
				'unique',
				'targetClass' => IssueStage::class,
				'filter' => function (QueryInterface $query) {
					if (!$this->getModel()->isNewRecord) {
						$query->andWhere(['not', ['id' => $this->getModel()->id]]);
					}
				},
			],
			[
				'typesIds', 'each',
				'rule' => [
					'in', 'range' => array_keys(IssueType::getTypesNames()),
				],
			],
		];
	}

	public function getModel(): IssueStage {
		if ($this->model === null) {
			$this->model = new IssueStage();
		}
		return $this->model;
	}

	public function setModel(IssueStage $model): void {
		$this->model = $model;
		$this->name = $model->name;
		$this->short_name = $model->short_name;
		$this->posi = $model->posi;
		$this->days_reminder = $model->days_reminder;
		$this->calendar_background = $model->calendar_background;
		$this->typesIds = array_map('intval', $model->getTypes()->select('id')->column());
	}

	public function attributeLabels(): array {
		return array_merge(IssueStage::instance()->attributeLabels(), [
			'typesIds' => Yii::t('issue', 'Types'),
		]);
	}

	public function save(bool $validate = true) {
		if ($validate && !$this->validate()) {
			return false;
		}

		$model = $this->getModel();
		$model->name = $this->name;
		$model->short_name = $this->short_name;
		$model->posi = $this->posi;
		$model->calendar_background = $this->calendar_background;
		$oldDays = $this->getModel()->days_reminder;
		$model->days_reminder = $this->days_reminder;

		$isNewRecord = $model->isNewRecord;
		if (!$model->save()) {
			return false;
		}
		if (!$isNewRecord) {
			$model->unlinkAll('types', true);
			if ($oldDays !== $model->days_reminder) {
				static::updateIssuesStageDeadlineAt($model->id, $model->days_reminder);
			}
		}

		foreach ($this->typesIds as $typeId) {
			$model->link('types', IssueType::get($typeId));
		}

		return true;
	}

	public static function updateIssuesStageDeadlineAt(int $stageId, ?int $days): int {
		$count = 0;
		if ($days) {
			$count += Issue::updateAll([
				'stage_deadline_at' => new Expression("DATE_ADD(stage_change_at, INTERVAL $days DAY)"),
			],
				'stage_id = :stageId AND stage_change_at IS NOT NULL',
				['stageId' => $stageId]
			);
			$count += Issue::updateAll([
				'stage_deadline_at' => new Expression("DATE_ADD(created_at, INTERVAL $days DAY)"),
			],
				'stage_id = :stageId AND stage_change_at IS NULL',
				['stageId' => $stageId]
			);
		} else {
			$count += Issue::updateAll([
				'stage_deadline_at' => null,
			],
				[
					'stage_id' => $stageId,
				]
			);
		}

		return $count;
	}

}

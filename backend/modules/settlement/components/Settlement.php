<?php

namespace backend\modules\settlement\components;

use common\models\issue\Issue;
use common\models\issue\StageType;
use yii\base\Component;

class Settlement extends Component {

	private static ?array $STAGES_TYPES = null;

	public function shouldCreateCalculation(Issue $issue): bool {
		if (!$this->existMinCalculationSettings()) {
			return false;
		}
		$minCount = $this->getMinCalculationCount($issue->type_id, $issue->stage_id);
	}

	public function getMinCalculationCount(int $type_id, int $stage_id): int {
		foreach (static::getStagesTypes() as $stageType) {
			if ($stageType->type_id === $type_id && $stageType->stage_id === $stage_id) {
				return $stageType->min_calculation_count;
			}
		}
		return 0;
	}

	public function existMinCalculationSettings(): bool {
		return empty(static::getStagesTypes());
	}

	/**
	 * @return StageType[]
	 */
	private static function getStagesTypes(): array {
		if (static::$STAGES_TYPES === null) {
			static::$STAGES_TYPES = StageType::find()
				->andWhere(['>', 'min_calculation_count', 0])
				->all();
		}
		return static::$STAGES_TYPES;
	}
}

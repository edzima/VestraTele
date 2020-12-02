<?php

namespace backend\modules\settlement\models\search;

use common\models\issue\Issue;
use common\models\issue\IssueSearch;
use common\models\issue\StageType;
use yii\data\ActiveDataProvider;
use yii\db\QueryInterface;

/**
 * Class IssueToCreateCalculationSearch
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class IssueToCreateCalculationSearch extends IssueSearch {

	public function rules(): array {
		return [
			[['issue_id', 'clientSurname', 'type_id', 'stage_id'], 'integer'],
		];
	}

	public function search(array $params): ActiveDataProvider {
		if (!$this->existMinCalculationSettings()) {
			return new ActiveDataProvider(['query' => Issue::find()->andWhere('0=1')]);
		}

		$query = Issue::find()
			->withoutArchives()
			->joinWith([
				'stageType' => function (QueryInterface $query) {
					$query->andWhere(['>', 'min_calculation_count', 0]);
				},
			]);
		$provider = new ActiveDataProvider(['query' => $query]);

		$query->andWhere([Issue::tableName() . '.id' => $this->issue_id]);
		return $provider;
	}

	public function existMinCalculationSettings(): bool {
		return StageType::find()
			->andWhere(['>', 'min_calculation_count', 0])
			->exists();
	}

}

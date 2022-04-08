<?php

namespace backend\modules\settlement\models\search;

use common\models\issue\Issue;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueSearch;
use common\models\issue\IssueType;
use common\models\issue\StageType;
use common\models\user\CustomerSearchInterface;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * Class IssueToCreateCalculationSearch
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class IssueToCreateCalculationSearch extends IssueSearch {

	public function rules(): array {
		return [
			[['issue_id', 'stage_id'], 'integer'],
			['type_id', 'in', 'range' => IssueType::getTypesIds(), 'allowArray' => true],
			['customerLastname', 'string', 'min' => CustomerSearchInterface::MIN_LENGTH],
		];
	}

	public function search(array $params): ActiveDataProvider {
		$query = Issue::find()
			->with('type')
			->with('stage')
			->with('customer.userProfile')
			->distinct()
			->addSelect([
				'issue.*',
				'(ST.min_calculation_count - IF(count(PC.issue_id) IS NOT NULL, '
				. 'count(PC.issue_id), 0)) as to_create',
			])
			->withoutArchives()
			->joinWith('stageType ST')
			->leftJoin(IssuePayCalculation::tableName() . ' PC', 'issue.id = PC.issue_id AND issue.stage_id = PC.stage_id')
			->andWhere(['>', 'ST.min_calculation_count', 0])
			->groupBy(['issue.id', 'issue.stage_id'])
			->having('to_create > 0');

		$provider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->existMinCalculationSettings() || !$this->validate()) {
			$query->andWhere('0=1');

			return $provider;
		}

		$this->applyCustomerNameFilter($query);

		$query->andFilterWhere([Issue::tableName() . '.id' => $this->issue_id])
			->andFilterWhere([Issue::tableName() . '.type_id' => $this->type_id])
			->andFilterWhere([Issue::tableName() . '.stage_id' => $this->stage_id]);

		return $provider;
	}

	public function existMinCalculationSettings(): bool {
		return !empty($this->getStagesNames());
	}

	public function getStagesNames(): array {
		return ArrayHelper::map(
			StageType::find()
				->with('stage')
				->andWhere(['>', 'min_calculation_count', 0])
				->asArray()
				->all(),
			'stage_id',
			'stage.name'
		);
	}

}

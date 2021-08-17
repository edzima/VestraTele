<?php

namespace common\models\provision;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Expression;

class ToUserGroupProvisionSearch extends ProvisionSearch {

	public function rules(): array {
		return [
			['to_user_id', 'integer'],
			[['dateFrom', 'dateTo'], 'safe'],
		];
	}

	public function search(array $params): ActiveDataProvider {
		$provider = parent::search($params);
		/** @var ActiveQuery $query */
		$query = $provider->query;
		$query->select(['to_user_id', new Expression('SUM(provision.value) as value')]);
		$query->groupBy('to_user_id');
		$provider->key = 'to_user_id';
		return $provider;
	}

	public function getSum(ActiveQuery $query): string {
		$query = clone($query);
		return Yii::$app->formatter->asCurrency($query->sum('value'));
	}
}

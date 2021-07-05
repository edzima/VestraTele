<?php

namespace common\modules\lead\models\searches;

use common\modules\lead\models\LeadReminder;
use common\modules\reminder\models\ReminderQuery;
use common\modules\reminder\models\searches\ReminderSearch;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\db\QueryInterface;

class LeadReminderSearch extends ReminderSearch {

	public const SCENARIO_USER = 'user';

	public ?int $user_id = null;

	public function rules(): array {
		return array_merge([
			['!user_id', 'required', 'on' => static::SCENARIO_USER],
		], parent::rules());
	}

	public function search(array $params): DataProviderInterface {
		$query = LeadReminder::find()
			->joinWith('lead');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			$query->andWhere('0=1');
		}

		$query->joinWith([
			'reminder' => function (ReminderQuery $query) {
				$this->applyReminderFilter($query);
			},
		]);

		if ($this->scenario === static::SCENARIO_USER) {
			if (empty($this->user_id)) {
				throw new InvalidConfigException('User Id cannot be blank on User scenario.');
			}
			$query->joinWith([
				'lead.leadUsers' => function (QueryInterface $query) {
					$query->andWhere(['user_id' => $this->user_id]);
				},
			]);
		}

		return $dataProvider;
	}

}

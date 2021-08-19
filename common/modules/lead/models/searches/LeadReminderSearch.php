<?php

namespace common\modules\lead\models\searches;

use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReminder;
use common\modules\reminder\models\ReminderQuery;
use common\modules\reminder\models\searches\ReminderSearch;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\db\ActiveQuery;
use yii\db\QueryInterface;

class LeadReminderSearch extends ReminderSearch {

	public const SCENARIO_USER = 'user';

	public ?string $leadName = null;
	public ?int $user_id = null;

	public function rules(): array {
		return array_merge([
			['!user_id', 'required', 'on' => static::SCENARIO_USER],
			['leadName', 'trim'],
			['leadName', 'string', 'min' => 3],
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

		/*
		$query->joinWith([
			'lead' => function (LeadQuery $query) {
				$this->applyReminderFilter($query);
			},
		]);

		*/

		$query->joinWith([
			'reminder' => function (ReminderQuery $query) {
				$this->applyReminderFilter($query);
			},
		]);
		$this->applyLeadNameFilter($query);

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

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
				'leadName' => \Yii::t('lead', 'Lead Name'),
			]
		);
	}

	private function applyLeadNameFilter(ActiveQuery $query) {
		if (!empty($this->leadName)) {
			$query->andFilterWhere(['like', Lead::tableName() . '.name', $this->leadName]);
		}
	}

}

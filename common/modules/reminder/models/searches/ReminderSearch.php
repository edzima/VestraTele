<?php

namespace common\modules\reminder\models\searches;

use common\modules\reminder\models\Reminder;
use common\modules\reminder\models\ReminderQuery;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ReminderSearch represents the model behind the search form of `common\modules\reminder\models\Reminder`.
 */
class ReminderSearch extends Reminder {

	protected const REMINDER_USER_AS_NULL = -1;

	public ?bool $onlyToday = false;
	public ?bool $onlyDelayed = true;

	public bool $hideDone = true;

	public ?string $dateStart = null;
	public ?string $dateEnd = null;

	public bool $userFilterWithNotSet = false;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['id', 'priority', 'created_at', 'updated_at'], 'integer'],
			[['date_at', 'details', 'dateStart', 'dateEnd'], 'safe'],
			[['onlyDelayed', 'onlyToday'], 'boolean'],
			['user_id', 'default', 'value' => static::REMINDER_USER_AS_NULL],
		];
	}

	public function attributeLabels(): array {
		return array_merge(
			parent::attributeLabels(), [
			'onlyToday' => Yii::t('common', 'Only Today'),
			'onlyDelayed' => Yii::t('common', 'Only Delayed'),
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function scenarios(): array {
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function search(array $params) {
		$query = Reminder::find();

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		// grid filtering conditions
		$this->applyReminderFilter($query);

		return $dataProvider;
	}

	protected function applyReminderFilter(ReminderQuery $query): void {
		$this->applyDateFilter($query);
		$this->applyHideDoneFilter($query);
		$this->applyUserFilter($query);
		$query->andFilterWhere([
			'priority' => $this->priority,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		]);
		$query->andFilterWhere(['like', 'details', $this->details]);
	}

	public function applyUserFilter(ReminderQuery $query): void {
		if ((int) $this->user_id === static::REMINDER_USER_AS_NULL) {
			$query->andWhere(Reminder::tableName() . '.user_id IS NULL');
			return;
		}
		if (!empty($this->user_id)) {
			$query->onlyUser($this->user_id, $this->userFilterWithNotSet);
		}
	}

	protected function applyDateFilter(ReminderQuery $query): void {
		if ($this->onlyToday) {
			$query->onlyToday();
		}
		if ($this->onlyDelayed) {
			$query->onlyDelayed();
		}
		$query->andFilterWhere(['>=', Reminder::tableName() . '.date_at', $this->dateStart]);
		$query->andFilterWhere(['<=', Reminder::tableName() . '.date_at', $this->dateEnd]);
	}

	protected function applyHideDoneFilter(ReminderQuery $query) {
		if ($this->hideDone) {
			$query->andWhere(Reminder::tableName() . '.done_at IS NULL');
		}
	}
}

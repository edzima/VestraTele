<?php

namespace common\models\provision;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * ProvisionSearch represents the model behind the search form of `common\models\provision\Provision`.
 */
class ProvisionSearch extends Provision {

	public $issue_id;
	public $onlyPayed = true;
	public $dateFrom;
	public $dateTo;
	public $clientSurname;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['onlyPayed', 'hide_on_report'], 'boolean'],
			[['pay_id', 'from_user_id', 'to_user_id', 'issue_id'], 'integer'],
			[['dateFrom', 'dateTo', 'clientSurname'], 'safe'],
		];
	}

	public function attributeLabels(): array {
		return array_merge([
			'onlyPayed' => 'Tylko opłacone',
			'dateFrom' => 'Data od',
			'dateTo' => 'Data do',
		], parent::attributeLabels());
	}

	public function init() {
		if (empty($this->dateFrom)) {
			$this->dateFrom = date('Y-m-d', strtotime('first day of this month'));
		}
		if (empty($this->dateTo)) {
			$this->dateTo = date('Y-m-d', strtotime('last day of this month'));
		}
		parent::init();
	}

	/**
	 * {@inheritdoc}
	 */
	public function scenarios() {
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	public function search(array $params): ActiveDataProvider {
		$query = Provision::find();
		$query
			->with('pay.issue')
			->with('type')
			->with('fromUser.userProfile')
			->with('toUser.userProfile');

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}

		if (!empty($this->issue_id)) {
			$query->joinWith('pay.issue');
			$query->andWhere(['issue.id' => $this->issue_id]);
		}

		if (!empty($this->clientSurname)) {
			$query->joinWith('pay.issue');
			$query->andFilterWhere(['like', 'issue.client_surname', $this->clientSurname]);
		}

		if ($this->onlyPayed) {
			$query->joinWith('pay');
			$query->andWhere('issue_pay.pay_at IS NOT NULL');
		}
		if ($this->hide_on_report) {
			$query->andWhere(['provision.hide_on_report' => true]);
		}

		// grid filtering conditions
		$query->andFilterWhere([
			'pay_id' => $this->pay_id,
			'to_user_id' => $this->to_user_id,
			'from_user_id' => $this->from_user_id,
		]);
		$this->dateFilter($query);

		return $dataProvider;
	}

	public function getFromUserList(): array {
		$query = Provision::find()
			->select('from_user_id')
			->groupBy('from_user_id')
			->joinWith('fromUser.userProfile');
		$this->dateFilter($query);
		return ArrayHelper::map($query->all(), 'from_user_id', 'fromUser.fullName');
	}

	public function getToUsersList(): array {
		$query = Provision::find()
			->select('to_user_id')
			->groupBy('to_user_id')
			->joinWith('toUser.userProfile');
		$this->dateFilter($query);
		return ArrayHelper::map($query->all(), 'to_user_id', 'toUser.fullName');
	}

	protected function dateFilter(ActiveQuery $query): void {
		if (!empty($this->dateFrom) || !empty($this->dateTo)) {
			$query
				->joinWith('pay')
				->andFilterWhere(['>=', 'issue_pay.pay_at', $this->dateFrom])
				->andFilterWhere(['<=', 'issue_pay.pay_at', $this->dateTo]);
		}
	}
}

<?php

namespace common\models\provision\search;

use common\models\issue\IssueCost;
use common\models\provision\Provision;
use common\models\provision\ProvisionQuery;
use common\models\SearchModel;
use common\models\user\CustomerSearchInterface;
use common\models\user\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\db\Expression;
use yii\db\QueryInterface;

class ReportSearch extends Model implements
	SearchModel,
	CustomerSearchInterface {

	public $user_id;
	public $dateFrom;
	public $dateTo;

	public $issue_id;
	public $customerLastname;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['user_id', 'issue_id'], 'integer'],
			[['dateFrom', 'dateTo'], 'safe'],
			['customerLastname', 'string', 'min' => CustomerSearchInterface::MIN_LENGTH],
		];
	}

	public function search(array $params): DataProviderInterface {

		$provision = Provision::find()
			->alias('p')
			->select(['p.to_user_id user_id', 'SUM(p.value) provisionsSum', new Expression('NULL as costsSum')])
			->groupBy('p.to_user_id');

		$cost = IssueCost::find()
			->alias('IC')
			->andWhere('IC.user_id IS NOT NULL')
			->select(['IC.user_id user_id', new Expression('NULL as provisionsSum'), 'SUM(IC.value) costsSum'])
			->groupBy('IC.user_id');

		/*
		$query = User::find()
			->select(['user_id id', 'SUM(provisionsSum) provisionsSum', 'SUM(costsSum) costsSum', 'SUM( costsSum + provisionsSum ) total'])
			->with('userProfile')
			->from($provision->union($cost))
			->groupBy('user_id');

		*/
		$query = User::find()
			->select(['user_id id', 'SUM(provisionsSum) provisionsSum', 'SUM(costsSum) costsSum', 'SUM( costsSum + provisionsSum ) total'])
			->from($provision->join('JOIN', IssueCost::tableName(), ['issue_cost.user_id' => 'p.user_id']));
		/*
		$query = User::find()
			->select(['user.id user_id', 'provisionsSum'])
			->joinWith([
				'provisions' => function (ProvisionQuery $query): void {
					$query->addSelect(['to_user_id user_id', 'SUM(p.value) provisionsSum']);
					$query->groupBy('to_user_id');
				},
			], false, 'JOIN');
		*/
		//	$query = $provision->union($cost)->groupBy('user_id');
		//->with('provisions.pay')
		//->with('issueCosts')

		//	var_dump($query->createCommand()->getRawSql());

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			//	$query->andWhere('0=1');
			//	return $dataProvider;
		}

		return $dataProvider;
	}

	public function getUsersList(): array {
		return [];
		return User::getSelectList(User::find()
			->select('user.id')
			->distinct()
			->joinWith('provisions')
			->column()
		);
	}

	public function applyCustomerSurnameFilter(QueryInterface $query): void {
		// TODO: Implement applyCustomerSurnameFilter() method.
	}
}

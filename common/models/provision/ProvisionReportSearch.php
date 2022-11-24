<?php

namespace common\models\provision;

use common\models\issue\IssueCost;
use common\models\user\User;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\DataProviderInterface;

/**
 * Class ProvisionReportSearch
 */
class ProvisionReportSearch extends ProvisionSearch {

	public int $limit = 500;

	public function setToUser(User $user): void {
		$this->toUser = $user;
		$this->to_user_id = $user->id;
	}

	public function rules(): array {
		return [
			['!to_user_id', 'required'],
			['payStatus', 'in', 'range' => array_keys(static::getPayStatusNames())],
			['payStatus', 'default', 'value' => static::DEFAULT_PAY_STATUS],
			[['dateFrom', 'dateTo', 'from_user_id'], 'safe'],
		];
	}

	public function search(array $params): ActiveDataProvider {
		$provider = parent::search($params);
		$provider->sort = false;
		$provider->pagination->defaultPageSize = $this->limit;
		$provider->pagination->pageSizeLimit = false;

		/* @var $query ProvisionQuery */
		$query = $provider->query;
		$query->notHidden();
		$query->joinWith('pay.calculation.pays');

		return $provider;
	}

	public function getNotSettledCosts(): DataProviderInterface {
		return new ArrayDataProvider([
			'allModels' => IssueCost::find()
				->indexBy('id')
				->with([
					'issue',
					'issue.customer.userProfile',
					'settlements',
				])
				->user($this->to_user_id)
				->notSettled()
				->andWhere(['between', 'date_at', $this->dateFrom, $this->dateTo])
				->all(),
			'pagination' => false,
		]);
	}

	public function getSettledCosts(): DataProviderInterface {
		return new ArrayDataProvider([
			'allModels' => IssueCost::find()
				->indexBy('id')
				->with([
					'issue',
					'issue.customer.userProfile',
				])
				->user($this->to_user_id)
				->settled($this->dateFrom, $this->dateTo)
				->all(),
			'pagination' => false,

		]);
	}

	public function hasHiddenProvisions(): bool {
		$query = Provision::find()
			->hidden()
			->andFilterWhere(['to_user_id' => $this->to_user_id]);

		$this->applyDateFilter($query);
		return $query->exists();
	}

	public function hasHiddenCost(): bool {
		return IssueCost::find()
			->hidden()
			->andFilterWhere(['>=', 'date_at', $this->dateFrom])
			->andFilterWhere(['<=', 'date_at', $this->dateTo])
			->user($this->to_user_id)
			->exists();
	}

	public function summary(): ProvisionReportSummary {
		if ($this->toUser === null) {
			throw new InvalidConfigException('Not Found User with ID: ' . $this->to_user_id);
		}
		$summary = new ProvisionReportSummary($this->toUser);
		$summary->provisionsDataProvider = $this->search([]);
		$summary->settledCostsDataProvider = $this->getSettledCosts();
		$summary->notSettledCostsDataProvider = $this->getNotSettledCosts();
		return $summary;
	}

}

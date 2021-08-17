<?php

namespace common\models\provision;

use common\models\issue\IssueCost;
use common\models\user\User;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\DataProviderInterface;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * Class ProvisionReportSearch
 */
class ProvisionReportSearch extends ProvisionSearch {

	public int $limit = 100;

	public function setToUser(User $user): void {
		$this->toUser = $user;
		$this->to_user_id = $user->id;
	}

	public function rules(): array {
		return [
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

		return $provider;
	}

	public function getSum(ActiveQuery $query): string {
		$query = clone($query);
		return Yii::$app->formatter->asCurrency($query->sum('provision.value'));
	}

	public function getNotSettledCosts(): DataProviderInterface {
		return new ArrayDataProvider([
			'allModels' => IssueCost::find()
				->indexBy('id')
				->with('issue')
				->user($this->to_user_id)
				->notSettled()
				->andWhere(['between', 'date_at', $this->dateFrom, $this->dateTo])
				->all(),
		]);
	}

	public function getSettledCosts(): DataProviderInterface {
		return new ArrayDataProvider([
			'allModels' => IssueCost::find()
				->indexBy('id')
				->with('issue')
				->user($this->to_user_id)
				->settled($this->dateFrom, $this->dateTo)
				->all(),
		]);
	}

	public function hasHiddenProvisions(): bool {
		$query = Provision::find()
			->hidden()
			->andFilterWhere(['to_user_id' => $this->to_user_id]);

		$this->applyDateFilter($query);
		return $query->exists();
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

	public function getFromUserList(): array {
		$query = Provision::find()
			->select('from_user_id')
			->groupBy('from_user_id')
			->andWhere(['to_user_id' => $this->to_user_id])
			->andWhere(['<>', 'from_user_id', $this->to_user_id])
			->joinWith('fromUser.userProfile');
		$this->applyDateFilter($query);
		return ArrayHelper::map($query->all(), 'from_user_id', 'fromUser.fullName');
	}

}

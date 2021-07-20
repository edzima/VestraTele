<?php

namespace common\models\provision;

use common\models\user\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * Class ProvisionReportSearch
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 *
 */
class ProvisionReportSearch extends ProvisionSearch {

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
		$provider->pagination->defaultPageSize = 100;
		$provider->pagination->pageSizeLimit = [1, 100];

		/* @var $query ProvisionQuery */
		$query = $provider->query;
		$query->notHidden();

		return $provider;
	}

	public function getSum(ActiveQuery $query): string {
		$query = clone($query);
		return Yii::$app->formatter->asCurrency($query->sum('provision.value'));
	}

	public function hasHiddenProvisions(): bool {
		$query = Provision::find()
			->hidden();

		$this->dateFilter($query);
		$query->andFilterWhere(['to_user_id' => $this->to_user_id]);
		return $query->exists();
	}

	public function getFromUserList(): array {
		$query = Provision::find()
			->select('from_user_id')
			->groupBy('from_user_id')
			->andWhere(['to_user_id' => $this->to_user_id])
			->andWhere(['<>', 'from_user_id', $this->to_user_id])
			->joinWith('fromUser.userProfile');
		$this->dateFilter($query);
		return ArrayHelper::map($query->all(), 'from_user_id', 'fromUser.fullName');
	}

}

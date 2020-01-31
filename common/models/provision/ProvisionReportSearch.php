<?php

namespace common\models\provision;

use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * Class ProvisionReportSearch
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 *
 * @property-read User $user
 */
class ProvisionReportSearch extends ProvisionSearch {

	public function setToUser(User $user): void {
		$this->toUser = $user;
		$this->to_user_id = $user->id;
	}

	public function rules(): array {
		return [
			[['dateFrom', 'dateTo'], 'safe'],
		];
	}

	public function search(array $params): ActiveDataProvider {
		$provider = parent::search($params);
		$provider->sort = false;
		$provider->pagination->defaultPageSize = 100;
		$provider->pagination->pageSizeLimit = [1, 100];

		$provider->query->andWhere([
			'or',
			['provision.hide_on_report' => false],
			['provision.hide_on_report' => null],
		]);

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

}

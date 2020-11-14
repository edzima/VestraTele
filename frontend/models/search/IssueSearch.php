<?php

namespace frontend\models\search;

use common\models\issue\IssueSearch as BaseIssueSearch;
use common\models\issue\IssueUser;
use common\models\issue\query\IssueQuery;
use common\models\user\User;
use common\models\user\Worker;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\QueryInterface;

class IssueSearch extends BaseIssueSearch {

	public int $user_id;
	public ?array $agentsIds = null;

	public function rules(): array {
		return array_merge(parent::rules(), [
			['agent_id', 'in', 'range' => $this->getAvailableAgentsIds()],
		]);
	}

	/**
	 * @param array $params
	 * @return ActiveDataProvider
	 * @throws InvalidConfigException
	 */
	public function search(array $params): ActiveDataProvider {
		if (empty($this->user_id)) {
			throw new InvalidConfigException('user_id must be set.');
		}
		$query = IssueUser::find();
		$query->joinWith([
			'issue' => function (IssueQuery $query): void {
				$query->with($this->issueWith());
				$this->issueQueryFilter($query);
			},
		]);

		$query->distinct('issue_id');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'key' => 'issue_id',
			'sort' => [
				'attributes' => [
					'issue_id',
					'issue.created_at',
					'issue.updated_at',
				],
				'defaultOrder' => ['issue.updated_at' => SORT_DESC],
			],
		]);

		$this->load($params);

		if (!$this->validate()) {
			$query->andFilterWhere([
				'issue_user.user_id' => $this->user_id,
			]);

			return $dataProvider;
		}

		// grid filtering conditions
		$query->andFilterWhere([
			'issue_user.user_id' => empty($this->agentsIds) ? $this->user_id : array_merge($this->agentsIds, [$this->user_id]),
		]);
		return $dataProvider;
	}

	protected function getAvailableAgentsIds(): array {
		if (!empty($this->agentsIds)) {
			$ids = $this->agentsIds;
			if (!in_array($this->user_id, $ids)) {
				$ids[] = $this->user_id;
			}
			return $ids;
		}
		return IssueUser::find()
			->select('user_id')
			->andWhere(['user_id' => $this->user_id])
			->withType(IssueUser::TYPE_AGENT)
			->column();
	}

	public function getAgentsList(): array {
		return Worker::getSelectList([User::ROLE_AGENT, User::PERMISSION_ISSUE], true, function (QueryInterface $query) {
			$query->andWhere(['id' => $this->getAvailableAgentsIds()]);
		});
	}

}

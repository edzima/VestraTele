<?php

namespace frontend\models\search;

use common\models\issue\IssueSearch as BaseIssueSearch;
use common\models\issue\IssueTag;
use common\models\issue\IssueUser;
use common\models\issue\query\IssueQuery;
use common\models\user\User;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class IssueSearch extends BaseIssueSearch {

	public int $user_id;
	public array $includedUsersIds = [];
	public array $excludedUsersIds = [];
	public bool $withArchive = true;
	public bool $withArchiveDeep = true;

	private ?array $availableAgentsIds = null;

	public function rules(): array {
		return array_merge(parent::rules(), [
			['agent_id', 'in', 'range' => $this->getAvailableAgentsIds()],
		]);
	}

	public function attributeLabels(): array {
		$labels = parent::attributeLabels();
		$labels['entity_responsible_id'] = Yii::t('issue', 'Entity');
		return $labels;
	}

	public static function getTagsNames(): array {
		return ArrayHelper::map(IssueTag::find()
			->andWhere(['is_active' => true])
			->asArray()
			->all(), 'id', 'name');
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
		$query->with('issue.claims');

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
			'issue_user.user_id' => empty($this->includedUsersIds) ? $this->user_id : array_merge($this->includedUsersIds, [$this->user_id]),
		]);
		$query->groupBy('issue_user.issue_id');

		$query->andFilterWhere(['NOT IN', 'issue_user.user_id', $this->excludedUsersIds]);
		return $dataProvider;
	}

	protected function getAvailableAgentsIds(): array {
		if ($this->availableAgentsIds === null) {
			$selfUserIds = IssueUser::find()
				->from([
					IssueUser::tableName() . ' IU_1',
					IssueUser::tableName() . ' IU_2',
				])
				->andWhere(['IU_1.user_id' => $this->user_id])
				->andWhere('IU_1.issue_id = IU_2.issue_id')
				->andWhere(['IU_2.type' => IssueUser::TYPE_AGENT])
				->select('IU_2.user_id')
				->distinct()
				->column();

			$ids = array_unique(array_merge([],$selfUserIds, $this->includedUsersIds));
			if (!in_array($this->user_id, $ids)) {
				$ids[] = $this->user_id;
			}
			$this->availableAgentsIds = $ids;
		}
		return $this->availableAgentsIds;
	}

	public function getAgentsNames(): array {
		return User::getSelectList(
			IssueUser::find()
				->select('user_id')
				->withType(IssueUser::TYPE_AGENT)
				->andWhere(['user_id' => $this->getAvailableAgentsIds()])
				->distinct()
				->column()
		);
	}

}

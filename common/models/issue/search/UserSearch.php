<?php

namespace common\models\issue\search;

use common\models\issue\IssueUser;
use common\models\issue\query\IssueQuery;
use common\models\user\SurnameSearchInterface;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\QueryInterface;

/**
 * UserSearch represents the model behind the search form of `common\models\issue\IssueUser`.
 *
 */
class UserSearch extends IssueUser implements
	ArchivedIssueSearch,
	SurnameSearchInterface {

	public string $surname = '';
	public bool $withArchive = false;

	/**
	 * {@inheritdoc}
	 */

	public function rules(): array {
		return [
			[['issue_id'], 'integer'],
			[['type'], 'string'],
			['surname', 'string', 'min' => SurnameSearchInterface::MIN_LENGTH],

		];
	}

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
			'surname' => Yii::t('common', 'Surname'),
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function scenarios(): array {
		return Model::scenarios();
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function search(array $params): ActiveDataProvider {
		$query = IssueUser::find();
		$query->joinWith([
			'issue' => function (IssueQuery $query): void {
				if (!$this->getWithArchive()) {
					$query->withoutArchives();
				}
			},
		]);
		$query->joinWith('user.userProfile');

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => 'issue.updated_at DESC',
			],
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}

		$this->applySurnameFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			'issue_id' => $this->issue_id,
			'type' => $this->type,
		]);

		return $dataProvider;
	}

	public function getWithArchive(): bool {
		return $this->withArchive;
	}

	public function applySurnameFilter(QueryInterface $query): void {
		if (!empty($this->surname)) {
			$query->with('user.userProfile');
			$query->andWhere(['like', 'user_profile.lastname', $this->surname . '%', false]);
		}
	}
}

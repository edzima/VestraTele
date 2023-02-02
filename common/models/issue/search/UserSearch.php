<?php

namespace common\models\issue\search;

use common\models\issue\IssueUser;
use common\models\issue\query\IssueQuery;
use common\models\query\PhonableQuery;
use common\models\user\SurnameSearchInterface;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\QueryInterface;

/**
 * UserSearch represents the model behind the search form of `common\models\issue\IssueUser`.
 *
 */
class UserSearch extends IssueUser implements
	ArchivedIssueSearch,
	SurnameSearchInterface {

	public string $surname = '';
	public string $phone = '';
	public bool $withArchive = false;
	public bool $withArchiveDeep = false;

	/**
	 * {@inheritdoc}
	 */

	public function rules(): array {
		return [
			[['issue_id'], 'integer'],
			[['type', 'phone'], 'string'],
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
		$this->applyPhoneFilter($query);

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

	public function getWithArchiveDeep(): bool {
		return $this->withArchiveDeep;
	}

	public function applySurnameFilter(QueryInterface $query): void {
		if (!empty($this->surname)) {
			$query->andWhere(['like', 'user_profile.lastname', $this->surname . '%', false]);
		}
	}

	public function applyPhoneFilter(ActiveQuery $query): void {
		if (!empty($this->phone)) {
			$query->joinWith([
				'user.userProfile' => function (PhonableQuery $query) {
					$query->withPhoneNumber($this->phone);
				},
			]);
		}
	}
}

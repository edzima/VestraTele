<?php

namespace backend\modules\issue\models\search;

use common\models\issue\IssueNote;
use common\models\user\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * IssueNoteSearch represents the model behind the search form of `common\models\issue\IssueNote`.
 */
class IssueNoteSearch extends IssueNote {

	public static function getUsersNames(): array {
		return User::getSelectList(
			IssueNote::find()
				->select('user_id')
				->distinct()
				->column(),
			false
		);
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['id', 'issue_id', 'user_id'], 'integer'],
			[['is_pinned', 'is_template'], 'boolean'],
			[['title', 'description', 'publish_at', 'created_at', 'updated_at', 'type'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios(): array {
		// bypass scenarios() implementation in the parent class
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
		$query = IssueNote::find();
		$query->joinWith('issue');
		$query->joinWith('user.userProfile');

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => [
				'defaultOrder' => [
					'updated_at' => SORT_DESC,
					'created_at' => SORT_DESC,
				],
			],
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		// grid filtering conditions
		$query->andFilterWhere([
			IssueNote::tableName() . '.id' => $this->id,
			IssueNote::tableName() . '.issue_id' => $this->issue_id,
			IssueNote::tableName() . '.user_id' => $this->user_id,
			IssueNote::tableName() . '.publish_at' => $this->publish_at,
			IssueNote::tableName() . '.created_at' => $this->created_at,
			IssueNote::tableName() . '.updated_at' => $this->updated_at,
			IssueNote::tableName() . '.is_pinned' => $this->is_pinned,
			IssueNote::tableName() . '.is_template' => $this->is_template,
		]);

		$query->andFilterWhere(['like', IssueNote::tableName() . '.title', $this->title])
			->andFilterWhere(['like', IssueNote::tableName() . '.type', $this->type])
			->andFilterWhere(['like', IssueNote::tableName() . '.description', $this->description]);

		return $dataProvider;
	}
}

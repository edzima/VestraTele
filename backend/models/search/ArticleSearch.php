<?php

namespace backend\models\search;

use common\models\Article;
use common\models\user\User;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ArticleSearch represents the model behind the search form about `common\models\Article`.
 */
class ArticleSearch extends Article {

	public static function authorNames(): array {
		return User::getSelectList(
			Article::find()
				->select('author_id')
				->distinct()
				->column()
		);
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['id', 'status', 'category_id', 'author_id', 'updater_id', 'published_at', 'created_at', 'updated_at', 'show_on_mainpage'], 'integer'],
			[['title', 'slug', 'body'], 'safe'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios() {
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	/**
	 * Creates data provider instance with search query applied.
	 *
	 * @param array $params
	 * @return ActiveDataProvider
	 */
	public function search($params) {
		$query = Article::find();

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => [
				'pageSize' => 30,
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
			'id' => $this->id,
			'status' => $this->status,
			'category_id' => $this->category_id,
			'author_id' => $this->author_id,
			'show_on_mainpage' => $this->show_on_mainpage,
			'updater_id' => $this->updater_id,
			'published_at' => $this->published_at,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
		]);

		$query->andFilterWhere(['like', 'title', $this->title])
			->andFilterWhere(['like', 'slug', $this->slug])
			->andFilterWhere(['like', 'body', $this->body]);

		return $dataProvider;
	}
}

<?php

namespace common\models\provision;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * ProvisionUserSearch represents the model behind the search form of `common\models\provision\ProvisionUser`.
 */
class ProvisionUserSearch extends ProvisionUser {

	public $onlySelf;
	public $fromUsername;
	public $toUsername;
	public $onlyNotDefault;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['onlySelf', 'onlyNotDefault'], 'boolean'],
			[['from_user_id', 'to_user_id', 'type_id'], 'integer'],
			[['value'], 'number'],
			[['fromUsername', 'toUsername'], 'safe'],
		];
	}

	public function attributeLabels(): array {
		return parent::attributeLabels() + [
				'onlySelf' => 'Tylko własne',
				'onlyNotDefault' => 'Tylko nadpisane',
			];
	}

	/**
	 * {@inheritdoc}
	 */
	public function scenarios() {
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
	public function search($params) {
		$query = ProvisionUser::find();
		$query->with(['fromUser.userProfile', 'toUser.userProfile', 'type']);

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		if ($this->onlySelf) {
			$query->andWhere('from_user_id = to_user_id');
		}

		// grid filtering conditions
		$query->andFilterWhere([
			'from_user_id' => $this->from_user_id,
			'to_user_id' => $this->to_user_id,
			'type_id' => $this->type_id,
			'value' => $this->value,
		]);

		if (!empty($this->fromUsername)) {
			$query->joinWith('fromUser fU');
			$query->andFilterWhere(['like', 'fU.username', $this->fromUsername]);
		}
		if (!empty($this->toUsername)) {
			$query->joinWith('toUser tU');
			$query->andFilterWhere(['like', 'tU.username', $this->toUsername]);
		}
		if ($this->onlyNotDefault) {
			$query->joinWith('type T');
			$query->andWhere('provision_user.value != T.value');
		}

		return $dataProvider;
	}

	public static function getTypesNames(): array {
		return ArrayHelper::map(Yii::$app->provisions->getTypes(), 'id', 'name');
	}
}

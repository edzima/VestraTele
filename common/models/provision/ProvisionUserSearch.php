<?php

namespace common\models\provision;

use common\models\SearchModel;
use common\models\user\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * ProvisionUserSearch represents the model behind the search form of `common\models\provision\ProvisionUser`.
 */
class ProvisionUserSearch extends ProvisionUser implements SearchModel {

	public $onlySelf;

	public $overwritten;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['onlySelf', 'overwritten'], 'boolean'],
			[['from_user_id', 'to_user_id', 'type_id'], 'integer'],
			[['value'], 'number'],
		];
	}

	public function attributeLabels(): array {
		return parent::attributeLabels() + [
				'onlySelf' => Yii::t('provision', 'Only self'),
				'overwritten' => Yii::t('provision', 'Overwritten'),
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
	public function search(array $params): ActiveDataProvider {
		$query = ProvisionUser::find();
		$query->with(['fromUser.userProfile', 'toUser.userProfile', 'type']);

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$this->load($params);

		if (!$this->validate()) {
			return $dataProvider;
		}

		$this->applyOnlySelfFilter($query);
		$this->applyOnlyOverwrittenFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			'from_user_id' => $this->from_user_id,
			'to_user_id' => $this->to_user_id,
			'type_id' => $this->type_id,
			'value' => $this->value,
		]);

		return $dataProvider;
	}

	private function applyOnlySelfFilter(ProvisionUserQuery $query): void {
		if ($this->onlySelf) {
			$query->onlySelf();
		}
	}

	private function applyOnlyOverwrittenFilter(ProvisionUserQuery $query): void {
		if ($this->overwritten !== null) {
			if ($this->overwritten) {
				$query->onlyNotOverwritten();

				return;
			}
			$query->onlyOverwritten();
		}
	}

	public static function fromUsersNames(): array {
		return User::getSelectList(
			ProvisionUser::find()
				->select('from_user_id')
				->distinct()
				->column()
		);
	}

	public static function toUsersNames(): array {
		return User::getSelectList(
			ProvisionUser::find()
				->select('to_user_id')
				->distinct()
				->column()
		);
	}

	public static function getTypesNames(): array {
		return ArrayHelper::map(Yii::$app->provisions->getTypes(), 'id', 'name');
	}
}

<?php

namespace backend\modules\user\models\search;

use common\models\Address;
use common\models\query\PhonableQuery;
use common\models\SearchModel;
use common\models\user\query\UserQuery;
use common\models\user\SurnameSearchInterface;
use common\models\user\User;
use common\models\user\UserTrait;
use common\validators\PhoneValidator;
use edzima\teryt\models\Simc;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\Sort;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class UserSearch extends User implements SurnameSearchInterface, SearchModel {

	public $firstname;
	public $lastname;
	public $phone;
	public $gender;
	public $region_id;
	public $city;
	public $addressInfo;

	public $role = [];
	public $permission = [];
	public $trait = [];

	public array $defaultOrder = [
		'action_at' => SORT_DESC,
	];

	public function attributeLabels(): array {
		return array_merge(
			parent::attributeLabels(),
			[
				'role' => Yii::t('common', 'Role'),
				'permission' => Yii::t('common', 'Permission'),
				'trait' => Yii::t('common', 'Trait'),
				'phone' => Yii::t('common', 'Phone number'),
			]
		);
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['id', 'status', 'created_at', 'updated_at', 'action_at', 'gender', 'region_id'], 'integer'],
			[['username', 'email', 'ip', 'firstname', 'lastname', 'phone', 'city', 'addressInfo'], 'safe'],
			['lastname', 'string', 'min' => SurnameSearchInterface::MIN_LENGTH],
			['role', 'in', 'range' => array_keys(static::getRolesNames()), 'allowArray' => true],
			['permission', 'in', 'range' => array_keys(static::getPermissionsNames()), 'allowArray' => true],
			['trait', 'in', 'range' => array_keys(static::getUserTraitsNames()), 'allowArray' => true],
			['phone', PhoneValidator::class],
		];
	}

	public static function getUserTraitsNames(): array {
		return UserTrait::getNames();
	}

	/**
	 * @inheritdoc
	 */
	public function scenarios(): array {
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	/**
	 * Creates data provider instance with search query applied.
	 *
	 * @param array $params
	 * @return ActiveDataProvider
	 */
	public function search(array $params): ActiveDataProvider {
		$query = $this->createQuery();
		$query->joinWith('userProfile');

		// add conditions that should always apply here

		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'sort' => $this->getSort(),
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		$this->applySurnameFilter($query);
		$this->applyAssigmentFilter($query);
		$this->applyTraitFilter($query);
		$this->applyPhoneFilter($query);

		// grid filtering conditions
		$query->andFilterWhere([
			'user.id' => $this->id,
			'status' => $this->status,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
			'action_at' => $this->action_at,
			'user_profile.gender' => $this->gender,
		]);

		$query->andFilterWhere(['like', 'username', $this->username])
			->andFilterWhere(['like', 'user_profile.firstname', $this->firstname])
			->andFilterWhere(['like', 'email', $this->email])
			->andFilterWhere(['like', Simc::tableName() . '.region_id', $this->region_id])
			->andFilterWhere(['like', Simc::tableName() . '.name', $this->city])
			->andFilterWhere(['like', Address::tableName() . '.info', $this->addressInfo])
			->andFilterWhere(['like', 'ip', $this->ip]);

		return $dataProvider;
	}

	protected function getSort(): Sort {
		return new Sort([
			'defaultOrder' => $this->defaultOrder,
			'attributes' => [
				'id',
				'username',
				'action_at',
				'firstname',
				'lastname',
				'city' => [
					'asc' => [Simc::tableName() . '.name' => SORT_ASC],
					'desc' => [Simc::tableName() . '.name' => SORT_DESC],
				],
				'addressInfo' => [
					'asc' => [Address::tableName() . '.info' => SORT_ASC],
					'desc' => [Address::tableName() . '.info' => SORT_DESC],
				],
				'email',
				'phone',
				'created_at',
				'updated_at',
			],
		]);
	}

	protected function createQuery(): UserQuery {
		return User::find();
	}

	private function applyPhoneFilter(UserQuery $query): void {
		if (!empty($this->phone)) {
			$query->joinWith([
				'userProfile' => function (PhonableQuery $query): void {
					$query->withPhoneNumber($this->phone);
				},
			]);
		}
	}

	protected function applySurnameFilter(UserQuery $query): void {
		if (!empty($this->lastname)) {
			$query->andFilterWhere(['like', 'user_profile.lastname', $this->lastname . '%', false]);
		}
	}

	protected function applyAssigmentFilter(UserQuery $query): void {
		$names = [];
		if (!empty($this->role)) {
			$names[] = $this->role;
		}
		if (!empty($this->permission)) {
			$names[] = $this->permission;
		}
		$names = array_merge(
			empty($this->role) ? [] : $this->role,
			empty($this->permission) ? [] : $this->permission
		);
		if (!empty($names)) {
			$query->onlyAssignments($names, true);
		}
	}

	private function applyTraitFilter(UserQuery $query): void {
		if (!empty($this->trait)) {
			$query->joinWith('traits T');
			$query->andWhere(['T.trait_id' => $this->trait]);
		}
	}

}

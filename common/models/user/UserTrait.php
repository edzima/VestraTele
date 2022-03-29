<?php

namespace common\models\user;

use common\helpers\ArrayHelper;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "user_trait".
 *
 * @property int $id
 * @property string|null $name
 * @property int $show_on_issue_view
 *
 * @property UserTraitAssign[] $userTraitAssigns
 * @property User[] $users
 */
class UserTrait extends ActiveRecord {

	private static $NAMES;

	/**
	 * {@inheritdoc}
	 */
	public static function tableName() {
		return '{{%user_trait}}';
	}

	public static function getNames(): array {
		if (empty(static::$NAMES)) {
			static::$NAMES = ArrayHelper::map(static::find()->asArray()->all(), 'id', 'name');
		}
		return static::$NAMES;
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name'], 'string', 'max' => 50],
			[['name'], 'unique'],
			[['show_on_issue_view'], 'boolean'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels() {
		return [
			'id' => Yii::t('common', 'ID'),
			'name' => Yii::t('common', 'Name'),
			'show_on_issue_view' => Yii::t('common', 'Show on Issue View'),
		];
	}

	/**
	 * Gets query for [[UserTraitAssigns]].
	 *
	 * @return ActiveQuery
	 */
	public function getUserTraitAssigns() {
		return $this->hasMany(UserTraitAssign::class, ['trait_id' => 'id']);
	}

	/**
	 * Gets query for [[Users]].
	 *
	 * @return ActiveQuery
	 */
	public function getUsers() {
		return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('user_trait_assign', ['trait_id' => 'id']);
	}
}

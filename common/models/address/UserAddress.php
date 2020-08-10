<?php

namespace app\models;

use common\models\address\Address;
use common\models\User;
use phpDocumentor\Reflection\Types\Array_;
use Yii;
use yii\base\Arrayable;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%user_address}}".
 *
 * @property int $user_id
 * @property int $city_id
 * @property string $type
 * @property int $subprovince_id
 * @property string $street
 * @property string $city_code
 * @property Address $address
 *
 * @property User $user
 */
class UserAddress extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%user_address}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['city_id', 'type', 'subservience_id', 'street', 'city_code'], 'required'],
            [['city_id', 'subprovince_id'], 'integer'],
            [['type', 'street', 'city_code'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'city_id' => 'City ID',
            'type' => 'Type',
            'subservience_id' => 'Subservience ID',
            'street' => 'Street',
            'city_code' => 'City Code',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getAddress():Address {
    	return Address::createFromCityId($this->city_id);
	}
}

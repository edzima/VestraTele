<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_address}}".
 *
 * @property int $user_id
 * @property int $city_id
 * @property string $type
 * @property int $subservience_id
 * @property string $street
 * @property int $city_code
 *
 * @property User $user
 */
class UserAddress extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
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
            [['city_id', 'subservience_id', 'city_code'], 'integer'],
            [['type', 'street'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
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
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}

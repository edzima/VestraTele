<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "miasta".
 *
 * @property integer $id
 * @property string $name
 * @property integer $wojewodztwo_id
 * @property integer $powiat_id
 */
class City extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'miasta';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'wojewodztwo_id', 'powiat_id'], 'integer'],
            [['name'], 'string', 'max' => 31],
            [['wojewodztwo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Wojewodztwa::className(), 'targetAttribute' => ['wojewodztwo_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'wojewodztwo_id' => 'Wojewodztwo ID',
            'powiat_id' => 'Powiat ID',
        ];
    }
	
	public static function getCitiesList($wojID, $powID){
		
		$cities = Self::find()->where("wojewodztwo_id=$wojID AND powiat_id=$powID")->all();
		foreach ($cities as $city) {
			$out[] = ['id' => $city['id'], 'name' => $city['name']];
		}
		return [
			'out' => $out,
			'selected' => $cities[0]['id']
			];
	}
}

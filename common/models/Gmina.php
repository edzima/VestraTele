<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "terc".
 *
 * @property integer $WOJ
 * @property integer $POW
 * @property integer $GMI
 * @property string $NAZWA
 */
class Gmina extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'terc';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['WOJ', 'POW', 'GMI'], 'integer'],
            [['NAZWA'], 'string', 'max' => 36],
            [['WOJ'], 'exist', 'skipOnError' => true, 'targetClass' => Wojewodztwa::className(), 'targetAttribute' => ['WOJ' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'WOJ' => 'Woj',
            'POW' => 'Pow',
            'GMI' => 'Gmi',
            'NAZWA' => 'Nazwa',
        ];
    }
	
	public static function getGminaList($wojID, $powID){
		
		$gminy = Self::find()->where("woj=$wojID AND POW =$powID")->all();
		foreach ($gminy as $gmina) {
			$out[] = ['id' => $gmina['id'], 'name' => $gmina['name']];
		}
		
		return [
			'out' => $out,
			'selected' => $gminy[0]['id']
			];
	}
}

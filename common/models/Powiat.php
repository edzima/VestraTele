<?php

namespace common\models;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "powiaty".
 *
 * @property integer $id
 * @property integer $wojewodztwo_id
 * @property integer $newID
 * @property string $name
 */
class Powiat extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'powiaty';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'wojewodztwo_id','name'], 'required'],
            [['id', 'wojewodztwo_id'], 'integer'],
            [['name'], 'string', 'max' => 25],
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
            'wojewodztwo_id' => 'Wojewodztwo',
            'name' => 'Nazwa',
        ];
    }
	
	
	public static function getPowiatListId($cat_id){	
		return self::find()->where(['wojewodztwo_id' => $cat_id])->asArray()->all();
        
	}
	
		
	public function getWojewodztwo(){
		return $this->hasOne(Wojewodztwa::className(),['id' => 'wojewodztwo_id']);
	}

    /**
     * @return newID
     */
    public function getNewID(){
        $newID = self::find()->where(['wojewodztwo_id' => $this->wojewodztwo_id])->max('id');
        $newID++;
        return $newID;

    }
	
}

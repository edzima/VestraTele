<?php

namespace common\models;

use Yii;


/**
 * This is the model class for table "task".
 *
 * @property integer $id
 * @property integer $tele_id
 * @property integer $agent_id
 * @property string $victim_name
 * @property string $phone
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $accident_id
 * @property integer $woj
 * @property integer $powiat
 * @property integer $gmina
 * @property integer $city
 * @property string $qualified_name
 * @property string $details
 * @property integer $meeting
 * @property integer $date
 */
class Task extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tele_id', 'agent_id', 'accident_id', 'woj', 'powiat', 'gmina', 'city'], 'number','min'=>1],
            [['details', 'meeting', 'agent_id', 'accident_id', 'city','victim_name','date'], 'required'],
            [['details'], 'string'],
            [['victim_name'], 'string', 'max' => 45, 'min' =>2],
            [['phone'], 'string', 'max' => 13],
            [['qualified_name'], 'string', 'max' => 200],
            [['accident_id'], 'exist', 'skipOnError' => true, 'targetClass' => AccidentTyp::className(), 'targetAttribute' => ['accident_id' => 'id']],
			[['city'], 'exist', 'skipOnError' => true, 'targetClass' => City::className(), 'targetAttribute' => ['city' => 'id']],
       
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tele_id' => 'Tele ID',
            'agent_id' => 'Przedstawiciel',
            'victim_name' => 'Poszkodowany',
            'phone' => 'Numer kontaktowy',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'accident_id' => 'Rodzaj zdarzenia',
            'woj' => 'Województwo',
            'powiat' => 'Powiat',
            'gmina' => 'Gmina',
            'city' => 'Miejscowość',
            'qualified_name' => 'Nazwiska upoważnionych',
            'details' => 'Szczegóły',
            'meeting' => 'Wstępnie umówione',
            'date' => 'Data spotkania',
        ];
    }

    /**
     * @inheritdoc
     * @return TaskQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TaskQuery(get_called_class());
    }
	
	/**
	* @return \yii\db\ActiveRelation
	*/
	public function getCityM() {
		return $this->hasOne(City::className(), ['id' => 'city']);
	}
	
	public function getCityName() {
		return $this->cityM->name;
	}
}

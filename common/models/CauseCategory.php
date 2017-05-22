<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "cause_category".
 *
 * @property int $id
 * @property string $name
 * @property int $period
 * @property int $color
 *
 * @property Cause[] $causes
 */
class CauseCategory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cause_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'period'], 'required'],
            [['period'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['color'], 'string', 'max' => 7],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Nazwa',
            'period' => 'Czas',
            'color' => 'Kolor',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCauses()
    {
        return $this->hasMany(Cause::className(), ['category_id' => 'id']);
    }

    public function getNameWithPeriod(){
        return $this->name.' ('.$this->period.')';
    }
}

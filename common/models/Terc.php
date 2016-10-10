<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "terc".
 *
 * @property string $WOJ
 * @property string $POW
 * @property string $GMI
 * @property integer $RODZ
 * @property string $NAZWA
 * @property string $NAZDOD
 * @property string $STAN_NA
 */
class Terc extends \yii\db\ActiveRecord
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
            [['RODZ'], 'integer'],
            [['STAN_NA'], 'safe'],
            [['WOJ', 'POW', 'GMI'], 'string', 'max' => 2],
            [['NAZWA'], 'string', 'max' => 36],
            [['NAZDOD'], 'string', 'max' => 50],
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
            'RODZ' => 'Rodz',
            'NAZWA' => 'Nazwa',
            'NAZDOD' => 'Nazdod',
            'STAN_NA' => 'Stan  Na',
        ];
    }
}

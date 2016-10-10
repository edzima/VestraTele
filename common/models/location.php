<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "simc".
 *
 * @property string $WOJ
 * @property string $POW
 * @property string $GMI
 * @property integer $RODZ_GMI
 * @property string $RM
 * @property string $MZ
 * @property string $NAZWA
 * @property string $SYM
 * @property string $SYMPOD
 * @property string $STAN_NA
 */
class Location extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'simc';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['RODZ_GMI'], 'integer'],
            [['MZ'], 'string'],
            [['SYM'], 'required'],
            [['STAN_NA'], 'safe'],
            [['WOJ', 'POW', 'GMI', 'RM'], 'string', 'max' => 2],
            [['NAZWA'], 'string', 'max' => 56],
            [['SYM', 'SYMPOD'], 'string', 'max' => 7],
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
            'RODZ_GMI' => 'Rodz  Gmi',
            'RM' => 'Rm',
            'MZ' => 'Mz',
            'NAZWA' => 'Nazwa',
            'SYM' => 'Sym',
            'SYMPOD' => 'Sympod',
            'STAN_NA' => 'Stan  Na',
        ];
    }
}

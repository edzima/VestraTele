<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "cause".
 *
 * @property int $id
 * @property string $victim_name
 * @property int $author_id
 * @property int $created_at
 * @property int $updated_at
 * @property int $date
 * @property int $category_id
 * @property int $is_finished
 *
 * @property User $author
 * @property CauseCategory $category
 */
class Cause extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cause';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [

            TimestampBehavior::className(),

        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['victim_name', 'author_id', 'category_id', 'date'], 'required'],
            [['author_id', 'created_at', 'updated_at',  'category_id', 'is_finished'], 'integer'],
            ['date', 'default',
                'value' => function () {
                    return date(DATE_ISO8601);
                }
            ],
            ['date', 'filter', 'filter' => 'strtotime'],
            [['victim_name'], 'string', 'max' => 255],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => CauseCategory::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'victim_name' => 'Imie i nazwisko',
            'author_id' => 'Author ID',
            'created_at' => 'Utworzono',
            'updated_at' => 'Aktualizacja',
            'date' => 'Rozpoczęcie etapu',
            'category_id' => 'Etap',
            'is_finished' => 'Archiwum',

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(CauseCategory::className(), ['id' => 'category_id']);
    }
}

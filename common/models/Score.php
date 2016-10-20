<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "score".
 *
 * @property integer $task_id
 * @property integer $tele_id
 * @property integer $connexion
 * @property integer $score
 * @property string $date
 */
class Score extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'score';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_id', 'tele_id', 'connexion', 'score', 'date', 'name'], 'required'],
            [['task_id', 'tele_id', 'connexion', 'score'], 'integer'],
            [['date'], 'safe'],
			[['name'], 'string', 'max'=>60],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::className(), 'targetAttribute' => ['task_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'task_id' => 'Task ID',
            'tele_id' => 'Konsultant',
            'connexion' => 'Zależność',
            'score' => 'Punkty',
            'date' => 'Data',
			'name' => 'Upoważniony'
        ];
    }
}

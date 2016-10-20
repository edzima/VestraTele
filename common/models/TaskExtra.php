<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "task_uncertain".
 *
 * @property integer $task_id
 * @property string $details
 * @property integer $count_agreement
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property integer $status
 */
class TaskExtra extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'task_uncertain';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_id', 'status'], 'required'],
            [['task_id', 'count_agreement', 'status'], 'integer'],
            [['details'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 120],
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
            'details' => 'Details',
            'count_agreement' => 'Ilość umów do dopisania',
            'name' => 'Kto do dopisania',
            'created_at' => 'Dodano',
            'updated_at' => 'Aktualizacja',
            'status' => 'Zakończono',
        ];
    }
}

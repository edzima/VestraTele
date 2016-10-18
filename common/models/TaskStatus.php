<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "task_status".
 *
 * @property integer $task_id
 * @property integer $answer_id
 * @property integer $count_agreement
 * @property string $status_details
 * @property string $name
 */
class TaskStatus extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'task_status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_id'], 'required'],
            [['task_id', 'answer_id', 'count_agreement'], 'integer'],
            [['status_details'], 'string'],
            [['name'], 'string', 'max' => 120],
            [['answer_id'], 'exist', 'skipOnError' => true, 'targetClass' => AnswerTyp::className(), 'targetAttribute' => ['answer_id' => 'id']],
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
            'answer_id' => 'Answer ID',
            'count_agreement' => 'Count Agreement',
            'status_details' => 'status_details',
            'name' => 'Name',
        ];
    }
	
	public function getTaskRel(){
		return $this->hasOne(Task::className(), ['id'=>'task_id']);
	}
}

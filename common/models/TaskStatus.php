<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "task_status".
 *
 * @property integer $task_id
 * @property integer $answer_id
 * @property integer $count_agreement
 * @property string $status_details
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property integer $finished
 * @property integer $extra_agreement
 * @property string $extra_name
 */
class TaskStatus extends ActiveRecord {

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'task_status';
	}

	public function beforeSave($insert) {
		if((int)$this->answer_id === AnswerTyp::AGREEMENT){
			$this->finished = true;
		}
		return parent::beforeSave($insert);
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['task_id', 'status_details', 'count_agreement', 'name'], 'required'],
			[['task_id', 'answer_id', 'count_agreement', 'finished', 'extra_agreement'], 'integer'],
			[['status_details'], 'string'],
			[['created_at', 'updated_at'], 'safe'],
			[['name'], 'string', 'max' => 120],
			[['extra_name'], 'string', 'max' => 250],
			[['answer_id'], 'exist', 'skipOnError' => true, 'targetClass' => AnswerTyp::className(), 'targetAttribute' => ['answer_id' => 'id']],
			[['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Task::className(), 'targetAttribute' => ['task_id' => 'id']],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'task_id' => 'Nr',
			'answer_id' => 'Efekt spotkania',
			'count_agreement' => 'Ilość umów',
			'status_details' => 'Komentarz',
			'name' => 'Kto poszkodowany',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'finished' => 'Zakończono',
			'extra_agreement' => 'Extra umowy',
			'extra_name' => 'Extra Poszkodowani',
		];
	}

	public function getAnswer() {
		return $this->hasOne(AnswerTyp::class, ['id' => 'answer_id']);
	}
}

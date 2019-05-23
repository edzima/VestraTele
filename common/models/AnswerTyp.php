<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "answer_typ".
 *
 * @property integer $id
 * @property string $name
 */
class AnswerTyp extends ActiveRecord {

	public const AGREEMENT = 1;

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'answer_typ';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['name'], 'string', 'max' => 45],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => 'ID',
			'name' => 'Name',
		];
	}
}

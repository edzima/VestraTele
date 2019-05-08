<?php

namespace common\models\issue;

/**
 * This is the model class for table "issue_pay".
 *
 * @property int $id
 * @property int $issue_id
 * @property string $date
 * @property string $value
 * @property int $type
 *
 * @property Issue $issue
 */
class IssuePay extends \yii\db\ActiveRecord {

	public const TYPE_HONORARIUM = 1;
	public const TYPE_COMPENSTAION = 2;

	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'issue_pay';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['issue_id', 'value'], 'required'],
			[['issue_id'], 'integer'],
			[['date'], 'safe'],
			[['type'], 'in', 'range' => array_keys(static::getTypesNames())],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::className(), 'targetAttribute' => ['issue_id' => 'id']],
		];
	}

	public function afterSave($insert, $changedAttributes) {
		$this->issue->touch('updated_at');
		parent::afterSave($insert, $changedAttributes);
	}

	public function afterDelete() {
		$this->issue->touch('updated_at');
		parent::afterDelete();
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
		return [
			'id' => 'ID',
			'issue_id' => 'Sprawa',
			'date' => 'Data',
			'value' => 'Wartość',
			'type' => 'Rodzaj',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getIssue() {
		return $this->hasOne(Issue::className(), ['id' => 'issue_id']);
	}

	public function getTypeName(): string {
		return static::getTypesNames()[$this->type];
	}

	public static function getTypesNames(): array {
		return [
			static::TYPE_HONORARIUM => 'Honorarium',
			static::TYPE_COMPENSTAION => 'Odszkodowanie',
		];
	}

}

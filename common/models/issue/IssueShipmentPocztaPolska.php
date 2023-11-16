<?php

namespace common\models\issue;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "issue_shipment_poczta_polska".
 *
 * @property int $issue_id
 * @property string $shipment_number
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $shipment_at
 * @property string|null $finished_at
 * @property string|null $apiData
 *
 * @property Issue $issue
 */
class IssueShipmentPocztaPolska extends ActiveRecord {

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%issue_shipment_poczta_polska}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['issue_id', 'shipment_number', 'created_at', 'updated_at'], 'required'],
			[['issue_id'], 'integer'],
			[['created_at', 'updated_at', 'shipment_at', 'finished_at'], 'safe'],
			[['apiData'], 'string'],
			[['shipment_number'], 'string', 'max' => 255],
			[['issue_id', 'shipment_number'], 'unique', 'targetAttribute' => ['issue_id', 'shipment_number']],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'issue_id' => Yii::t('issue', 'Issue ID'),
			'shipment_number' => Yii::t('issue', 'Shipment Number'),
			'created_at' => Yii::t('issue', 'Created At'),
			'updated_at' => Yii::t('issue', 'Updated At'),
			'shipment_at' => Yii::t('issue', 'Shipment At'),
			'finished_at' => Yii::t('issue', 'Finished At'),
			'apiData' => Yii::t('issue', 'Api Data'),
		];
	}

	/**
	 * Gets query for [[Issue]].
	 *
	 * @return ActiveQuery
	 */
	public function getIssue() {
		return $this->hasOne(Issue::class, ['id' => 'issue_id']);
	}
}

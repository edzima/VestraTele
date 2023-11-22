<?php

namespace common\models\issue;

use common\components\postal\models\Shipment;
use common\components\postal\models\ShipmentModelInterface;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "issue_shipment_poczta_polska".
 *
 * @property int $issue_id
 * @property string $shipment_number
 * @property string|null $details
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $shipment_at
 * @property string|null $finished_at
 * @property string|null $apiData
 *
 * @property Issue $issue
 */
class IssueShipmentPocztaPolska extends ActiveRecord implements
	IssueInterface,
	ShipmentModelInterface {

	use IssueTrait;

	public function behaviors(): array {
		return [
			[
				'class' => TimestampBehavior::class,
				'value' => new Expression('CURRENT_TIMESTAMP'),
			],
		];
	}

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
			[['issue_id', 'shipment_number'], 'required'],
			[['issue_id'], 'integer'],
			[['created_at', 'updated_at', 'shipment_at', 'finished_at'], 'safe'],
			[['apiData'], 'string'],
			[['shipment_number', 'details'], 'string', 'max' => 255],
			[['apiData', 'details'], 'trim'],
			[['apiData', 'details'], 'default', 'value' => null],
			[['issue_id', 'shipment_number'], 'unique', 'targetAttribute' => ['issue_id', 'shipment_number']],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'issue_id' => Yii::t('issue', 'Issue'),
			'shipment_number' => Yii::t('issue', 'Shipment Number'),
			'details' => Yii::t('issue', 'Details'),
			'created_at' => Yii::t('issue', 'Created At'),
			'updated_at' => Yii::t('issue', 'Updated At'),
			'shipment_at' => Yii::t('issue', 'Shipment At'),
			'finished_at' => Yii::t('issue', 'Finished At'),
			'apiData' => Yii::t('issue', 'Api Data'),
			'shipmentTypeName' => Yii::t('issue', 'Shipment Type'),
		];
	}

	public function getShipmentTypeName(): ?string {
		return $this->getShipment()->danePrzesylki->rodzPrzes ?? null;
	}

	public function isFinished(): bool {
		return !empty($this->finished_at);
	}

	/**
	 * Gets query for [[Issue]].
	 *
	 * @return ActiveQuery
	 */
	public function getIssue() {
		return $this->hasOne(Issue::class, ['id' => 'issue_id']);
	}

	public function getShipment(): ?Shipment {
		if (empty($this->apiData)) {
			return null;
		}
		return unserialize($this->apiData);
	}

	public function setShipment(Shipment $shipment = null): void {
		$this->shipment_at = null;
		$this->finished_at = null;
		if ($shipment === null) {
			$this->apiData = null;
			return;
		}
		$this->shipment_number = $shipment->numer;
		$this->apiData = serialize($shipment);

		if ($shipment->isOk()) {
			$details = $shipment->danePrzesylki;
			if ($details) {
				$this->shipment_at = $details->dataNadania;
				$this->finished_at = $details->getFinishedAt();
			}
		}
	}
}

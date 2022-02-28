<?php

namespace common\modules\lead\models;

use common\modules\lead\entities\DialerConfig;
use common\modules\lead\entities\DialerConfigInterface;
use common\modules\lead\entities\DialerInterface;
use common\modules\lead\entities\LeadDialerEntity;
use common\modules\lead\models\query\LeadDialerQuery;
use Yii;
use yii\base\InvalidArgumentException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * This is the model class for table "lead_dialer".
 *
 * @property int $id
 * @property int $lead_id
 * @property int $type_id
 * @property int $status
 * @property int|null $priority
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $last_at
 * @property string|null $dialer_config
 *
 * @property Lead $lead
 * @property LeadDialerType $type
 *
 * @property-read string|null $dialerStatusName
 * @property-read DialerInterface|null $dialer
 */
class LeadDialer extends ActiveRecord {

	public const PRIORITY_LOW = 0;
	public const PRIORITY_MEDIUM = 5;
	public const PRIORITY_HIGH = 10;

	public static function toCallStatuses(): array {
		return [
			LeadDialerEntity::STATUS_NEW,
			LeadDialerEntity::STATUS_NOT_ESTABLISH,
		];
	}

	public function behaviors(): array {
		return [
			'timestamp' => [
				'class' => TimestampBehavior::class,
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%lead_dialer}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['lead_id', 'type_id'], 'required'],
			[['lead_id', 'type_id', 'priority', 'status'], 'integer'],
			[['created_at', 'updated_at'], 'safe'],
			[['dialer_config'], 'string'],
			[['lead_id'], 'exist', 'skipOnError' => true, 'targetClass' => Lead::class, 'targetAttribute' => ['lead_id' => 'id']],
			[['type_id'], 'exist', 'skipOnError' => true, 'targetClass' => LeadDialerType::class, 'targetAttribute' => ['type_id' => 'id']],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('lead', 'ID'),
			'lead_id' => Yii::t('lead', 'Lead ID'),
			'type_id' => Yii::t('lead', 'Type ID'),
			'priority' => Yii::t('lead', 'Priority'),
			'created_at' => Yii::t('lead', 'Created At'),
			'updated_at' => Yii::t('lead', 'Updated At'),
			'dialer_config' => Yii::t('lead', 'Dialer Config'),
			'status' => Yii::t('lead', 'Status'),
			'statusName' => Yii::t('lead', 'Status'),
			'dialerStatusName' => Yii::t('lead', 'Dialer Status'),
		];
	}

	/**
	 * Gets query for [[Lead]].
	 *
	 * @return ActiveQuery
	 */
	public function getLead() {
		return $this->hasOne(Lead::class, ['id' => 'lead_id']);
	}

	/**
	 * Gets query for [[Type]].
	 *
	 * @return ActiveQuery
	 */
	public function getType() {
		return $this->hasOne(LeadDialerType::class, ['id' => 'type_id']);
	}

	public function getConfig(): DialerConfigInterface {
		return new DialerConfig(Json::decode($this->dialer_config));
	}

	public function getStatusName(): string {
		return static::getStatusesNames()[$this->status];
	}

	public function getDialerStatusName(): ?string {
		$dialer = $this->getDialer();
		if ($dialer) {
			return static::getStatusesNames()[$dialer->getStatusId()];
		}
		return null;
	}

	public function getDialer(): ?DialerInterface {
		try {
			return new LeadDialerEntity($this);
		} catch (InvalidArgumentException $e) {
			$message = 'LeadDialer: ' . $this->id . ' without valid Dialer. ' . $e->getMessage();
			Yii::warning($message, 'lead.LeadDialer.dialer');
		}
		return null;
	}

	public static function getPriorityNames(): array {
		return [
			static::PRIORITY_LOW => Yii::t('lead', 'Low Priority'),
			static::PRIORITY_MEDIUM => Yii::t('lead', 'Medium Priority'),
			static::PRIORITY_HIGH => Yii::t('lead', 'High Priority'),
		];
	}

	public static function getStatusesNames(): array {
		return LeadDialerEntity::getStatusesNames();
	}

	public static function find(): LeadDialerQuery {
		return new LeadDialerQuery(static::class);
	}

}

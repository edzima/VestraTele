<?php

namespace common\models\issue;

use common\helpers\ArrayHelper;
use common\models\entityResponsible\EntityResponsible;
use common\models\issue\query\IssueQuery;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "issue_claim".
 *
 * @property int $id
 * @property int $issue_id
 * @property string $type
 * @property float|null $trying_value
 * @property float|null $obtained_value
 * @property float|null $percent_value
 * @property string|null $details
 * @property string $date
 * @property int $entity_responsible_id
 *
 *
 * @property-read EntityResponsible $entityResponsible
 *
 * @property Issue $issue
 */
class IssueClaim extends ActiveRecord implements IssueInterface {

	use IssueTrait;

	public const TYPE_CUSTOMER = 'customer';
	public const TYPE_COMPANY = 'company';

	public const SCENARIO_TYPE = 'type';

	public function isCompany(): bool {
		return $this->type === static::TYPE_COMPANY;
	}

	public function isCustomer(): bool {
		return $this->type === static::TYPE_CUSTOMER;
	}

	public function getTypeWithEntityName(): string {
		return $this->getTypeName() . ' -> ' . $this->entityResponsible->name;
	}

	public static function getEntityResponsibleNames(): array {
		return ArrayHelper::map(EntityResponsible::find()->asArray()->all(), 'id', 'name');
	}

	public function formName(): string {
		$name = parent::formName();
		if ($this->scenario === static::SCENARIO_TYPE) {
			$name .= '-' . $this->type;
		}
		return $name;
	}

	public function getTypeName(): string {
		return static::getTypesNames()[$this->type];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%issue_claim}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['issue_id', 'type', 'entity_responsible_id', 'date'], 'required'],
			[['!type'], 'required', 'on' => static::SCENARIO_TYPE],
			[['issue_id'], 'integer'],
			[['trying_value', 'obtained_value', 'percent_value'], 'number', 'min' => 0],
			[['type'], 'string', 'max' => 10],
			[['details'], 'string', 'max' => 255],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
			['type', 'in', 'range' => array_keys(static::getTypesNames())],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('issue', 'ID'),
			'issue_id' => Yii::t('issue', 'Issue ID'),
			'type' => Yii::t('issue', 'Type'),
			'typeName' => Yii::t('issue', 'Who'),
			'trying_value' => Yii::t('issue', 'Trying Value'),
			'obtained_value' => Yii::t('issue', 'Obtained Value'),
			'percent_value' => Yii::t('issue', '%'),
			'details' => Yii::t('issue', 'Details'),
			'entity_responsible_id' => Yii::t('issue', 'Entity Responsible'),
			'entityResponsible' => Yii::t('issue', 'Entity Responsible'),
			'date' => Yii::t('issue', 'Date At'),

		];
	}

	public function getIssue(): IssueQuery {
		return $this->hasOne(Issue::class, ['id' => 'issue_id']);
	}

	public function getEntityResponsible(): ActiveQuery {
		return $this->hasOne(EntityResponsible::class, ['id' => 'entity_responsible_id']);
	}

	public static function getTypesNames(): array {
		return [
			static::TYPE_CUSTOMER => Yii::t('issue', 'Customer Claim'),
			static::TYPE_COMPANY => Yii::t('issue', 'Company Claim'),
		];
	}
}

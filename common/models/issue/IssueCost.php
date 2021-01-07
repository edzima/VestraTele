<?php

namespace common\models\issue;

use common\models\issue\query\IssueQuery;
use common\models\settlement\VATInfo;
use common\models\settlement\VATInfoTrait;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "issue_cost".
 *
 * @property int $id
 * @property int $issue_id
 * @property string $type
 * @property string $value
 * @property string|null $vat
 * @property int $created_at
 * @property int $updated_at
 * @property string $date_at
 *
 * @property-read Issue $issue
 * @property-read string $typeName
 */
class IssueCost extends ActiveRecord implements
	IssueInterface, VATInfo {

	use IssueTrait;
	use VATInfoTrait;

	public const TYPE_PURCHASE_OF_RECEIVABLES = 'purchase_of_receivables';
	public const TYPE_WRIT = 'writ';
	public const TYPE_JUSTIFICATION_OF_THE_JUDGMENT = 'justification_of_the_judgment';

	public static function tableName(): string {
		return '{{%issue_cost}}';
	}

	public function behaviors(): array {
		return [
			TimestampBehavior::class,
		];
	}

	public function getTypeName(): string {
		return static::getTypesNames()[$this->type];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'issue_id' => Yii::t('common', 'Issue'),
			'type' => Yii::t('common', 'Type'),
			'value' => Yii::t('common', 'Value with VAT'),
			'vat' => 'VAT (%)',
			'VATPercent' => 'VAT (%)',
			'created_at' => Yii::t('common', 'Created at'),
			'updated_at' => Yii::t('common', 'Updated at'),
			'date_at' => Yii::t('common', 'Date at'),
		];
	}

	public function getIssue(): IssueQuery {
		return $this->hasOne(Issue::class, ['id' => 'issue_id']);
	}

	public static function getTypesNames(): array {
		return [
			static::TYPE_PURCHASE_OF_RECEIVABLES => Yii::t('common', 'Purchase of receivables'),
			static::TYPE_WRIT => Yii::t('common', 'Writ'),
			static::TYPE_JUSTIFICATION_OF_THE_JUDGMENT => Yii::t('common', 'Justification of the judgment'),
		];
	}

}

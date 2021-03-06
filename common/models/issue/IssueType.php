<?php

namespace common\models\issue;

use common\models\issue\query\IssueStageQuery;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "issue_type".
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property int $provision_type
 * @property string $vat
 * @property bool $meet
 * @property bool $with_additional_date
 *
 * @property Issue[] $issues
 * @property IssueStage[] $stages
 * @property Provision $provision
 */
class IssueType extends ActiveRecord {

	private ?Provision $provision = null;

	private static ?array $TYPES = null;

	public function __toString(): string {
		return $this->name;
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName(): string {
		return '{{%issue_type}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['name', 'short_name', 'vat', 'provision_type'], 'required'],
			[['provision_type'], 'integer'],
			[['meet', 'with_additional_date'], 'boolean'],
			[['name', 'short_name'], 'string', 'max' => 255],
			[['name'], 'unique'],
			['vat', 'number', 'min' => 0, 'max' => 100],
			[['short_name'], 'unique'],
			['provision_type', 'in', 'range' => array_keys(Provision::getTypesNames())],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'name' => Yii::t('common', 'Name'),
			'short_name' => Yii::t('common', 'Shortname'),
			'provision_type' => Yii::t('common', 'Provision type'),
			'vat' => 'VAT (%)',
			'with_additional_date' => Yii::t('common', 'With additional Date'),
			'meet' => Yii::t('common', 'meet'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getIssues() {
		return $this->hasMany(Issue::class, ['type_id' => 'id']);
	}

	public function getStages(): IssueStageQuery {
		return $this->hasMany(IssueStage::class, ['id' => 'stage_id'])
			->viaTable('{{%issue_stage_type}}', ['type_id' => 'id']);
	}

	public function getProvision(): Provision {
		if ($this->provision === null) {
			$this->provision = new Provision($this->provision_type);
		}
		return $this->provision;
	}

	public function getNameWithShort(): string {
		return $this->name . ' (' . $this->short_name . ')';
	}

	public static function getTypesIds(): array {
		return array_keys(static::getTypes());
	}

	public static function getShortTypesNames(): array {
		return ArrayHelper::map(static::getTypes(), 'id', 'short_name');
	}

	public static function getTypesNames(): array {
		return ArrayHelper::map(static::getTypes(), 'id', 'name');
	}

	public static function get(int $typeId): ?self {
		return static::getTypes()[$typeId] ?? null;
	}

	/**
	 * @return static[]
	 */
	public static function getTypes(): array {
		if (empty(static::$TYPES)) {
			static::$TYPES = static::find()->indexBy('id')->all();
		}
		return static::$TYPES;
	}

}

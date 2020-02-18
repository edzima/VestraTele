<?php

namespace common\models\issue;

use yii\base\InvalidArgumentException;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "issue_type".
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property int $provision_type
 * @property string $vat
 * @property boolean $meet
 *
 * @property Issue[] $issues
 * @property IssueStage[] $stages
 * @property Provision $provision
 */
class IssueType extends ActiveRecord {

	public const ACCIDENT_ID = 1;
	public const SPA_ID = 2;
	private $provision;

	private static $TYPES = [];

	/**
	 * @inheritdoc
	 */
	public static function tableName(): string {
		return 'issue_type';
	}

	public static function get(int $typeId) {
		if (!isset(static::$TYPES[$typeId])) {
			$model = static::findOne($typeId);
			if ($model === null) {
				throw new InvalidArgumentException('Invalid type id: ' . $typeId);
			}
			static::$TYPES[$typeId] = $model;
		}
		return static::$TYPES[$typeId];
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['name', 'short_name', 'vat'], 'required'],
			[['provision_type'], 'integer'],
			['meet', 'boolean'],
			[['name', 'short_name'], 'string', 'max' => 255],
			[['name'], 'unique'],
			['vat', 'number', 'min' => 0, 'max' => 100],
			[['short_name'], 'unique'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'name' => 'Nazwa',
			'short_name' => 'Skrót',
			'provision_type' => 'Prowizja',
			'vat' => 'VAT (%)',
			'meet' => 'Spotkania',
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getIssues() {
		return $this->hasMany(Issue::class, ['type_id' => 'id']);
	}

	public function getStages() {
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

	public function __toString(): string {
		return $this->name;
	}
}

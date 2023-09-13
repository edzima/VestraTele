<?php

namespace common\models\entityResponsible;

use common\models\Address;
use common\models\issue\Issue;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "issue_entity_responsible".
 *
 * @property int $id
 * @property string $name
 * @property string|null $details
 * @property boolean $is_for_summon
 * @property int|null $address_id
 *
 * @property-read Issue[] $issues
 * @property-read Address|null $address
 */
class EntityResponsible extends ActiveRecord {

	public $issuesCount;

	public function __toString(): string {
		return $this->name;
	}

	/**
	 * @inheritdoc
	 */
	public static function tableName(): string {
		return '{{%issue_entity_responsible}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['name'], 'required'],
			[['name'], 'string', 'max' => 255],
			['details', 'string'],
			[['name'], 'unique'],
			[['is_for_summon'], 'boolean'],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels(): array {
		return [
			'id' => 'ID',
			'name' => Yii::t('common', 'Name'),
			'is_for_summon' => Yii::t('common', 'Show in summon'),
			'details' => Yii::t('common', 'Details'),
			'issuesCount' => Yii::t('common', 'Issues'),
		];
	}

	public function getAddress(): ActiveQuery {
		return $this->hasOne(Address::class, ['id' => 'address_id']);
	}

	/**
	 * @return ActiveQuery
	 */
	public function getIssues() {
		return $this->hasMany(Issue::class, ['entity_responsible_id' => 'id']);
	}

	public static function find(): EntityResponsibleQuery {
		return new EntityResponsibleQuery(static::class);
	}

	public function linkAddress(Address $model): void {
		$this->link('address', $model);
	}

	public function unlinkAddress(Address $model): void {
		if (!$model->isNewRecord) {
			$this->unlink('address', $model);
			$model->delete();
		}
	}

}

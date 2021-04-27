<?php

namespace common\modules\lead\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * This is the model class for table "lead_report_schema".
 *
 * @property int $id
 * @property string $name
 * @property string|null $placeholder
 * @property boolean $is_required
 * @property string $statuses
 * @property string $types
 * @property boolean show_in_grid
 *
 * @property LeadReport[] $reports
 * @property LeadReportSchemaStatusType[] $schemaStatusTypes
 */
class LeadReportSchema extends ActiveRecord {

	public function __toString(): string {
		return $this->name;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function tableName() {
		return '{{%lead_report_schema}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name'], 'required'],
			['show_in_grid', 'boolean'],
			[['name', 'placeholder'], 'string', 'max' => 255],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('lead', 'ID'),
			'name' => Yii::t('lead', 'Name'),
			'placeholder' => Yii::t('lead', 'Placeholder'),
		];
	}

	public function getTypesNames(): string {
		$names = [];
		foreach ($this->getTypesIds() as $id) {
			$names[$id] = LeadType::getModels()[$id]->name;
		}
		return implode(', ', $names);
	}

	public function getTypesIds(): array {
		if (empty($this->types)) {
			return [];
		}
		return Json::decode($this->types);
		//	return ArrayHelper::getColumn($this->schemaStatusTypes, 'type_id');
	}

	public function setStatusIds(array $ids): void {
		$this->statuses = Json::encode($ids);
	}

	public function setTypesIds(array $ids): void {
		$this->types = Json::encode($ids);
	}

	public function getStatusNames(): string {
		$names = [];
		foreach ($this->getStatusIds() as $id) {
			$names[$id] = LeadStatus::getModels()[$id]->name;
		}
		return implode(', ', $names);
	}

	public function getStatusIds(): array {
		if (empty($this->statuses)) {
			return [];
		}
		return Json::decode($this->statuses);
		return ArrayHelper::getColumn($this->schemaStatusTypes, 'status_id');
	}

	/**
	 * Gets query for [[LeadReports]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getReports() {
		return $this->hasMany(LeadReport::class, ['schema_id' => 'id']);
	}

	/**
	 * Gets query for [[LeadReportSchemaStatusTypes]].
	 *
	 * @return \yii\db\ActiveQuery
	 */
	public function getSchemaStatusTypes() {
		return $this->hasMany(LeadReportSchemaStatusType::class, ['schema_id' => 'id']);
	}

	/**
	 * @param int $status_id
	 * @param int $type_id
	 * @return static[]
	 */
	public static function findWithStatusAndType(int $status_id, int $type_id): array {
		return static::find()
			->joinWith('schemaStatusTypes')
			->andWhere(['or', ['status_id' => null], ['status_id' => $status_id]])
			->andWhere(['or', ['type_id' => null], ['type_id' => $type_id]])
			->all();
	}

}

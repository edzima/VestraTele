<?php

namespace common\modules\lead\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "lead_report_schema".
 *
 * @property int $id
 * @property string $name
 * @property string|null $placeholder
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
		return ArrayHelper::getColumn($this->schemaStatusTypes, 'type_id');
	}

	public function getStatusNames(): string {
		$names = [];
		foreach ($this->getStatusIds() as $id) {
			$names[$id] = LeadStatus::getModels()[$id]->name;
		}
		return implode(', ', $names);
	}

	public function getStatusIds(): array {
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

}

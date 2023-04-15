<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadSource;
use common\modules\lead\models\LeadStatus;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class LeadSourceChangeForm extends Model {

	public array $ids = [];
	public ?int $source_id = null;

	public function rules(): array {
		return [
			[['ids', 'source_id'], 'required'],
			['source_id', 'integer'],
			['source_id', 'in', 'range' => array_keys(static::getSourcesNames())],
		];
	}

	public function attributeLabels(): array {
		return [
			'source_id' => Yii::t('lead', 'Source'),
		];
	}

	public function save(): ?int {
		if (!$this->validate()) {
			return null;
		}
		return Lead::updateAll([
			'source_id' => $this->source_id,
		], ['id' => $this->ids]);
	}

	public static function getSourcesNames(): array {
		return LeadSource::getNames();
	}
}

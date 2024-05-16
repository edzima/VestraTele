<?php

namespace common\modules\lead\models\forms;

use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadSource;
use Yii;
use yii\base\Model;

class LeadSourceChangeForm extends Model {

	public array $ids = [];
	public ?int $source_id = null;
	public const SCENARIO_NOT_REQUIRED = 'not-required';

	public function rules(): array {
		return [
			[['ids', 'source_id'], 'required', 'except' => static::SCENARIO_NOT_REQUIRED],
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

	public function getSourceName(): ?string {
		return static::getSourcesNames()[$this->source_id] ?? null;
	}
}

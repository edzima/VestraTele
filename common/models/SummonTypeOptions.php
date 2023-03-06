<?php

namespace common\models;

use common\helpers\ArrayHelper;
use common\models\issue\form\SummonForm;
use common\models\issue\Summon;
use Yii;
use yii\base\Model;
use yii\helpers\Json;

class SummonTypeOptions extends Model {

	public bool $showOnTop = false;

	public $formAttributes = [];
	public $visibleSummonFields = [];
	public $requiredFields = [];
	public ?string $term = null;
	public ?string $title = null;
	public ?int $status = null;

	public function rules(): array {
		return [
			[['status'], 'integer'],
			[['showOnTop'], 'boolean'],
			['title', 'string'],
			['formAttributes', 'in', 'range' => array_keys(static::formAttributesNames()), 'allowArray' => true],
			['visibleSummonFields', 'in', 'range' => array_keys(static::visibleSummonAttributesNames()), 'allowArray' => true],
			['requiredFields', 'in', 'range' => array_keys(static::getRequiredAttributes()), 'allowArray' => true],
			['term', 'in', 'range' => array_keys(static::getTermsNames())],
			['status', 'in', 'range' => array_keys(static::getStatusesNames())],

		];
	}

	public static function formAttributesNames(): array {
		$attributes = static::formAttributes();
		$names = [];
		foreach ($attributes as $attribute) {
			$names[$attribute] = static::getFormAttributeLabel($attribute);
		}
		static::sortLabels($names);
		return $names;
	}

	protected static function formAttributes(): array {
		$attributes = static::formInstance()->attributes();
		foreach (static::excludesFormAttributes() as $attribute) {
			ArrayHelper::removeValue($attributes, $attribute);
		}
		return $attributes;
	}

	protected static function formInstance(): SummonForm {
		return SummonForm::instance();
	}

	protected static function excludesFormAttributes(): array {
		return [
			'issue_id',
			'type_id',
			'status',
			'updater_id',
		];
	}

	protected static function getFormAttributeLabel(string $attribute): string {
		return static::formInstance()->getAttributeLabel($attribute);
	}

	public static function sortLabels(array &$names): void {
		uasort($names, static function (string $a, string $b): int {
			if ($a === $b) {
				return 0;
			}
			return ($a < $b) ? -1 : 1;
		});
	}

	public static function visibleSummonAttributesNames(): array {
		$attributes = static::summonAttributes();
		$names = [];
		foreach ($attributes as $attribute) {
			$names[$attribute] = static::getSummonAttributeLabel($attribute);
		}
		static::sortLabels($names);
		return $names;
	}

	protected static function summonAttributes(): array {
		$attributes = static::summonInstance()->attributes();
		foreach (static::excludesSummonAttributes() as $attribute) {
			ArrayHelper::removeValue($attributes, $attribute);
		}
		return $attributes;
	}

	protected static function summonInstance(): Summon {
		return Summon::instance();
	}

	protected static function excludesSummonAttributes(): array {
		return [
			'id',
			'type_id',
			'status',
			'created_at',
			'updated_at',
			'updater_id'
		];
	}

	protected static function getSummonAttributeLabel(string $attribute): string {
		if ($attribute === 'deadline_at') {
			return Yii::t('common', 'Deadline At(Day)');
		}
		return static::summonInstance()->getAttributeLabel($attribute);
	}

	public static function getRequiredAttributes(): array {
		$attributes = static::formAttributes();
		$names = [];
		foreach ($attributes as $attribute) {
			$names[$attribute] = static::getFormAttributeLabel($attribute);
		}
		static::sortLabels($names);
		return $names;
	}

	public static function getTermsNames(): array {
		return SummonForm::getTermsNames();
	}

	public static function getStatusesNames(): array {
		return SummonForm::getStatusesNames();
	}

	public function attributeLabels(): array {
		return [
			'showOnTop' => Yii::t('common', 'Show on Top'),
			'formAttributes' => Yii::t('common', 'Form Attributes'),
			'visibleSummonFields' => Yii::t('common', 'Visible Summon Fields'),
			'requiredFields' => Yii::t('common', 'Required Fields'),
			'title' => Yii::t('common', 'Title'),
			'term' => Yii::t('common', 'Term'),
			'status' => Yii::t('common', 'Status'),
		];
	}

	public function toJson(): string {
		$data = $this->toArray();
		ksort($data);
		return Json::encode($data);
	}

	public function getFormAttributesNames(): array {
		if (empty($this->formAttributes)) {
			return [];
		}
		$names = [];
		foreach (static::formAttributesNames() as $field => $label) {
			if (in_array($field, $this->formAttributes, true)) {
				$names[$field] = $label;
			}
		}
		return $names;
	}

	public function getVisibleSummonAttributesNames(): array {
		if (empty($this->visibleSummonFields)) {
			return [];
		}
		$names = [];
		foreach (static::visibleSummonAttributesNames() as $field => $label) {
			if (in_array($field, $this->visibleSummonFields, true)) {
				$names[$field] = $label;
			}
		}
		return $names;
	}

	public function getRequiredFieldsNames(): array {
		if (empty($this->requiredFields)) {
			return [];
		}
		$names = [];
		foreach (static::formAttributesNames() as $field => $label) {
			if (in_array($field, $this->requiredFields, true)) {
				$names[$field] = $label;
			}
		}
		return $names;
	}

}

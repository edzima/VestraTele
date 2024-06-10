<?php

namespace common\modules\file\models;

use Yii;
use yii\base\Model;
use yii\helpers\Json;
use yii\validators\FileValidator;

/**
 * @see FileValidator
 */
class ValidatorOptions extends Model {

	public $maxSize;
	public $maxFiles;

	public $extensions;

	public string $validatorClass = FileValidator::class;

	public function createValidator(): FileValidator {
		$config = [];
		$config['class'] = $this->validatorClass;
		foreach ($this->getFileValidatorAttributes() as $attribute) {
			$config[$attribute] = $this->{$attribute};
		}
		return Yii::createObject($config);
	}

	public function getFileValidatorAttributes(): array {
		return [
			'maxSize',
			'maxFiles',
			'extensions',
		];
	}

	public function rules(): array {
		return [
			[['maxSize'], 'integer', 'min' => 1],
			[['maxFiles'], 'integer', 'min' => 0],
			[['extensions'], 'string'],
		];
	}

	public function attributeLabels(): array {
		return [
			'extensions' => Yii::t('file', 'Extensions'),
			'maxSize' => Yii::t('file', 'Max size'),
			'maxFiles' => Yii::t('file', 'Max files'),

		];
	}

	public function toJson(): string {
		$values = $this->toArray();
		return Json::encode($values);
	}

	public static function createFromJson(string $json): self {
		$values = Json::decode($json);
		return new static($values);
	}
}

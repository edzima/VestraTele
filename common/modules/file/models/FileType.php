<?php

namespace common\modules\file\models;

use common\helpers\ArrayHelper;
use common\helpers\FileHelper;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "file_type".
 *
 * @property int $id
 * @property string $name
 * @property int $is_active
 * @property string $visibility
 * @property string $validator_config
 * @property string $visibility_attributes
 *
 * @property File[] $files
 */
class FileType extends ActiveRecord {

	public const VISIBILITY_PRIVATE = 'private';
	public const VISIBILITY_PUBLIC = 'public';

	private ?ValidatorOptions $_validatorOptions = null;
	private ?VisibilityOptions $_visibilityOptions = null;

	private static $_instances = [];

	/**
	 * {@inheritdoc}
	 */
	public static function tableName(): string {
		return '{{%file_type}}';
	}

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['name', 'visibility', 'validator_config'], 'required'],
			[['is_active'], 'integer'],
			[['validator_config'], 'string'],
			[['name', 'visibility'], 'string', 'max' => 255],
			[['name'], 'unique'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'id' => Yii::t('file', 'ID'),
			'name' => Yii::t('file', 'Name'),
			'is_active' => Yii::t('file', 'Is Active'),
			'visibility' => Yii::t('file', 'Visibility'),
			'visibilityName' => Yii::t('file', 'Visibility'),
			'validator_config' => Yii::t('file', 'Validator Config'),
		];
	}

	/**
	 * Gets query for [[Files]].
	 *
	 * @return ActiveQuery
	 */
	public function getFiles() {
		return $this->hasMany(File::class, ['file_type_id' => 'id']);
	}

	public function getValidatorOptions(): ValidatorOptions {
		if ($this->_validatorOptions === null) {
			$this->_validatorOptions = ValidatorOptions::createFromJson((string) $this->validator_config);
		}
		return $this->_validatorOptions;
	}

	public function getVisibilityOptions(): VisibilityOptions {
		if ($this->_visibilityOptions === null) {
			$this->_visibilityOptions = VisibilityOptions::createFromJson((string) $this->visibility_attributes);
		}
		return $this->_visibilityOptions;
	}

	public function isPublic(): bool {
		return $this->visibility === static::VISIBILITY_PUBLIC;
	}

	public function getVisibilityName(): string {
		return static::getVisibilityNames()[$this->visibility];
	}

	public function getAcceptExtensions(): string {
		$accept = [];
		$extensions = $this->getValidatorOptions()->extensions;
		if (empty($extensions)) {
			return '*';
		}
		$extensions = explode(',', $extensions);
		foreach ($extensions as $extension) {
			$extension = trim($extension);
			$mime = FileHelper::getMimeTypeFromExtension($extension);
			if ($mime) {
				$accept[] = $mime;
			}
		}
		return implode(',', $accept);
	}

	public static function getVisibilityNames(): array {
		return [
			static::VISIBILITY_PUBLIC => Yii::t('file', 'Public'),
			static::VISIBILITY_PRIVATE => Yii::t('file', 'Private'),
		];
	}

	public static function getNames(bool $active): array {
		return ArrayHelper::map(
			static::getTypes($active),
			'id',
			'name'
		);
	}

	public static function getTypes(bool $active): array {
		if (empty(static::$_instances)) {
			static::$_instances = static::find()->all();
		}
		if ($active) {
			return array_filter(static::$_instances, function (FileType $model): bool {
				return $model->is_active;
			});
		}
		return static::$_instances;
	}

	public function isForUser(int $userId) {

	}

}

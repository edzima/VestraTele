<?php

namespace common\modules\file\models;

use common\modules\file\Module;
use Exception;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use yii\validators\FileValidator;
use yii\web\UploadedFile;

class UploadForm extends Model {

	/**
	 * @var UploadedFile[]|UploadedFile file attribute
	 */
	public $file;

	public int $userId;

	private FileType $type;
	private Module $module;

	private ?FileValidator $fileValidator = null;

	public function __construct(FileType $type, Module $module, $config = []) {
		$this->type = $type;
		$this->module = $module;
		parent::__construct($config);
	}

	public function getType(): FileType {
		return $this->type;
	}

	public function getValidators() {
		$validators = parent::getValidators();
		$fileValidator = $this->getFileValidator();
		$validators[] = $fileValidator;
		return $validators;
	}

	protected function getFileValidator(): FileValidator {
		if ($this->fileValidator === null) {
			$this->fileValidator = $this->type->getValidatorOptions()->createValidator();
			$this->fileValidator->attributes = ['file'];
		}
		return $this->fileValidator;
	}

	public function saveUploads(AttachableModel $model, bool $validate = true): ?int {
		if ($validate && !$this->validate()) {
			return null;
		}
		$files = UploadedFile::getInstances($this, 'file');
		$module = $this->module;
		$userTempDir = $module->getUserDirPath();
		if (!empty($files)) {
			foreach ($files as $file) {
				if (!$file->saveAs($userTempDir . $file->name)) {
					throw new Exception(Yii::t('yii', 'File upload failed.'));
				}
			}
		}
		$attachedFiles = [];
		foreach (FileHelper::findFiles($userTempDir) as $file) {
			if (!($attachedFile = $module->attachFile(
				$file,
				$model,
				$this->type->id,
				$this->userId)
			)) {
				throw new Exception(Yii::t('yii', 'File upload failed.'));
			} else {
				$attachedFiles[] = $attachedFile;
			}
		}
		rmdir($userTempDir);
		return count($attachedFiles);
	}

}

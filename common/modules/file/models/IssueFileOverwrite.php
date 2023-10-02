<?php

namespace common\modules\file\models;

use common\modules\file\Module;
use Exception;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\validators\FileValidator;
use yii\web\UploadedFile;

class IssueFileOverwrite extends Model {

	public $file;

	private ?FileValidator $fileValidator = null;

	private IssueFile $issueFile;
	private Module $module;

	public function __construct(IssueFile $issueFile, Module $module, $config = []) {
		$this->issueFile = $issueFile;
		$this->module = $module;
		parent::__construct($config);
	}

	public function getValidators() {
		$validators = parent::getValidators();
		$fileValidator = $this->getFileValidator();
		$validators[] = $fileValidator;
		return $validators;
	}

	protected function getFileValidator(): FileValidator {
		if ($this->fileValidator === null) {
			$this->fileValidator = $this->issueFile->file->fileType->getValidatorOptions()->createValidator();
			$this->fileValidator->attributes = ['file'];
		}
		return $this->fileValidator;
	}

	public function getIssueFile(): IssueFile {
		return $this->issueFile;
	}

	public function save(bool $validate = true): ?int {
		if ($validate && !$this->validate()) {
			return null;
		}
		$file = UploadedFile::getInstance($this, 'file');
		if ($file) {
			$module = $this->module;
			$userTempDir = $module->getUserDirPath();
			if (!$file->saveAs($userTempDir . $file->name)) {
				throw new Exception(Yii::t('yii', 'File upload failed.'));
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

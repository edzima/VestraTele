<?php

namespace common\modules\file\models;

use common\modules\file\Module;
use Exception;
use Maestroerror\HeicToJpg;
use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;
use yii\validators\FileValidator;
use yii\web\UploadedFile;

class UploadForm extends Model {

	/**
	 * @var UploadedFile[]|UploadedFile file attribute
	 */
	public $file;

	public int $userId;

	public bool $imageToWeb = true;
	public int $imageQuality = 75;

	private FileType $type;
	private Module $module;

	private ?FileValidator $fileValidator = null;

	public function __construct(FileType $type, Module $module, $config = []) {
		$this->type = $type;
		$this->module = $module;
		parent::__construct($config);
	}

	public function attributeLabels(): array {
		return [
			'file' => Yii::t('file', 'Files'),
		];
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
			if ($this->imageToWeb && $this->fileIsImage($file)) {
				$file = $this->imageToWebp($file);
			}
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

	public function fileIsImage(string $file): bool {
		$mime = FileHelper::getMimeType($file);
		return strpos($mime, 'image') !== false;
	}

	public function imageToWebp(string $file): string {
		if (HeicToJpg::isHeic($file)) {
			$newFile = $this->replace_extension($file, 'jpg');
			HeicToJpg::convert($file)->saveAs($this->replace_extension($file, 'jpg'));
			FileHelper::unlink($file);
			$file = $newFile;
		}

		$extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
		switch ($extension) {
			case 'jpg':
			case 'jpeg':
				$img = @imagecreatefromjpeg($file);
				break;
			case 'gif':
				$img = @imagecreatefromgif($file);
				break;
			case 'png':
				$img = @imagecreatefrompng($file);
				break;
			default:
				$img = false;
				break;
		}
		if ($img !== false) {
			// open an image file
			$newFile = $this->replace_extension($file, 'webp');
			if (imagewebp($img, $newFile, $this->imageQuality)) {
				FileHelper::unlink($file);
				$file = $newFile;
			} else {
				Yii::error([
					'msg' => 'Image to webp failed.',
					'file' => $file,
				], __METHOD__);
			}
			imagedestroy($img);
		}
		return $file;
	}

	function replace_extension($filename, $new_extension) {
		$info = pathinfo($filename);
		return $info['dirname'] . '/' . $info['filename'] . '.' . $new_extension;
	}

}

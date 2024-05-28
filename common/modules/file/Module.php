<?php

namespace common\modules\file;

use Closure;
use common\modules\file\models\AttachableModel;
use common\modules\file\models\File;
use common\modules\file\models\FileType;
use creocoder\flysystem\Filesystem;
use Yii;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\base\Module as BaseModule;
use yii\di\Instance;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;

class Module extends BaseModule {

	public $controllerNamespace = 'common\modules\file\controllers';

	public string $tempPath = '@runtime/temp-file';

	public array $rules = [];

	/**
	 * @var string|array|Filesystem
	 */
	public $filesystem = 'awss3Fs';

	public ?string $directory = null;

	public ?Closure $checkAccess = null;

	public function init() {
		parent::init();
		$this->filesystem = Instance::ensure($this->filesystem, Filesystem::class);
		$this->rules = ArrayHelper::merge($this->defaultRules(), $this->rules);
		$this->defaultRoute = 'file';
	}

	/**
	 * @return null|Module
	 * @throws \Exception
	 */
	public function getFlysystem(): Filesystem {
		return $this->filesystem;
	}

	public function getUserDirPath(): string {
		Yii::$app->session->open();

		$userDirPath = $this->getTempPath() . DIRECTORY_SEPARATOR . Yii::$app->session->getId();
		FileHelper::createDirectory($userDirPath);

		return $userDirPath . DIRECTORY_SEPARATOR;
	}

	public function getTempPath(): string {
		return Yii::getAlias($this->tempPath);
	}

	/**
	 * @param string $filePath
	 * @return bool|File
	 * @throws Exception
	 * @throws InvalidConfigException
	 */
	public function attachFile(string $filePath, AttachableModel $model, int $fileTypeId, int $userId = null): ?File {
		if (!file_exists($filePath)) {
			throw new Exception("File '{$filePath}' not exists.");
		}
		if ($userId === null) {
			$userId === Yii::$app->user->getId();
		}
		if ($userId === null) {
			throw new InvalidArgumentException('UserId is required.');
		}

		$file = File::compose($filePath);
		$file->file_type_id = $fileTypeId;
		$file->owner_id = $userId;
		$this->ensureFileName($file, $model->getDirParts());
		$file->path = $this->getFileFlyPath($file, $model->getDirParts());
		$flySystem = $this->getFlysystem();
		// copy to flysystem
		$stream = fopen($filePath, 'r+');
		$flySystem->write($file->path, $stream);
		fclose($stream);

		if ($file->save()) {
			unlink($filePath);
			$model->linkFile($file);
			return $file;
		}
		return null;
	}

	protected function ensureFileName(File $file, array $dirParts = []): void {
		$baseName = $file->name;
		$i = 0;
		$flySystem = $this->getFlysystem();
		while ($flySystem->has($this->getFileFlyPath($file, $dirParts))) {
			$file->name = $baseName . '_' . (++$i);
		}
	}

	private function getFileFlyPath(File $file, array $parts = []): string {
		$fileFullName = $file->getNameWithType();
		$parts[] = $fileFullName;
		return implode(DIRECTORY_SEPARATOR, $parts);
	}

	public function detachFile(File $file, bool $deleteModel = true): bool {
		$flysystem = $this->getFlysystem();
		$path = $file->path;
		if ($flysystem->has($path)) {
			$flysystem->delete($path);
		}
		if ($deleteModel) {
			return $file->delete();
		}
		return true;
	}

	public function findFileType(int $id): ?FileType {
		return FileType::findOne($id);
	}

	protected function defaultRules(): array {
		return [
			'maxFiles' => 3,
		];
	}
}

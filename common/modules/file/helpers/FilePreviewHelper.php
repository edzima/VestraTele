<?php

namespace common\modules\file\helpers;

use common\helpers\Url;
use common\modules\file\models\File;

class FilePreviewHelper {

	public array $previewRoute = [];
	public array $deleteRoute = [];
	public string $paramFileId = 'file_id';
	public string $defaultFileType = 'image';

	public function getInitialPreview(array $files): array {
		$preview = [];
		foreach ($files as $file) {
			$preview[] = $this->getInitialPreviewForFile($file);
		}
		return $preview;
	}

	public function getInitialPreviewForFile(File $file): string {
		$url = $this->previewRoute;
		$url[$this->paramFileId] = $file->id;
		return Url::to($url, true);
	}

	public function getInitialPreviewConfig(array $files): array {
		$preview = [];
		foreach ($files as $file) {
			$preview[] = $this->getInitialPreviewConfigForFile($file);
		}
		return $preview;
	}

	public function getInitialPreviewConfigForFile(File $file): array {
		$url = $this->deleteRoute;
		$url[$this->paramFileId] = $file->id;
		return [
			'caption' => $file->getNameWithType(),
			'url' => Url::to($url, true),
			'size' => $file->size,
			'type' => $this->getInitialPreviewConfigFileType($file),
		];
	}

	protected function getInitialPreviewConfigFileType(File $file): string {
		if (strpos($file->mime, 'image') !== false) {
			return 'image';
		}
		if (strpos($file->mime, 'pdf') !== false) {
			return 'pdf';
		}
		if (strpos($file->mime, 'video') !== false) {
			return 'video';
		}
		if (strpos($file->mime, 'text') !== false) {
			return 'text';
		}
		if (strpos($file->mime, 'opendocument') !== false) {
			return 'office';
		}
		return $this->defaultFileType;
	}

	public static function createForIssue(int $issueId): FilePreviewHelper {
		$static = new static();
		$static->previewRoute = ['/file/issue/download', 'issue_id' => $issueId];
		$static->deleteRoute = ['/file/issue/delete', 'issue_id' => $issueId];
		return $static;
	}
}

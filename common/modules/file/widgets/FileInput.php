<?php

namespace common\modules\file\widgets;

use common\helpers\Url;
use common\modules\file\models\File;
use kartik\file\FileInput as BaseFileInput;

class FileInput extends BaseFileInput {

	public array $previewRoute = [];
	public array $deleteRoute = [];

	public string $paramFileId = 'file_id';

	/**
	 * @var File[]
	 */
	public array $previewFiles = [];
	public string $defaultFileType = 'image';

	public bool $overwriteInitial = false;

	public function init() {
		parent::init();
		$this->pluginOptions['overwriteInitial'] = $this->overwriteInitial;
		$this->pluginOptions['fileActionSettings']['showDrag'] = false;
		if (!isset($this->pluginOptions['initialPreview'])) {
			$this->pluginOptions['initialPreview'] = $this->getInitialPreview();
			$this->pluginOptions['initialPreviewAsData'] = true;
		}
		if (!isset($this->pluginOptions['initialPreviewConfig'])) {
			$this->pluginOptions['initialPreviewConfig'] = $this->getInitialPreviewConfig();
		}
	}

	public function getInitialPreview(): array {
		if (empty($this->previewFiles) || empty($this->previewRoute)) {
			return [];
		}
		$initialPreview = [];
		foreach ($this->previewFiles as $file) {
			$url = $this->previewRoute;
			$url[$this->paramFileId] = $file->id;
			$initialPreview[] = Url::to($url, true);
		}
		return $initialPreview;
	}

	public function getInitialPreviewConfig(): array {
		if (empty($this->previewRoute) || empty($this->previewFiles)) {
			return [];
		}
		$initialPreviewConfig = [];
		foreach ($this->previewFiles as $file) {
			$url = $this->deleteRoute;
			$url[$this->paramFileId] = $file->id;
			$initialPreviewConfig[] = [
				'caption' => $file->getNameWithType(),
				'url' => Url::to($url, true),
				'size' => $file->size,
				'type' => $this->getInitialPreviewConfigFileType($file),
			];
		}
		return $initialPreviewConfig;
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
}

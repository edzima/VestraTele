<?php

namespace common\modules\file\widgets;

use common\modules\file\helpers\FilePreviewHelper;
use common\modules\file\models\File;
use kartik\file\FileInput as BaseFileInput;

class FileInput extends BaseFileInput {

	public FilePreviewHelper $filePreviewHelper;
	public array $previewRoute = [];
	public array $deleteRoute = [];
	/**
	 * @var File[]
	 */
	public array $previewFiles = [];
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
		return $this->filePreviewHelper->getInitialPreview($this->previewFiles);
	}

	public function getInitialPreviewConfig(): array {
		return $this->filePreviewHelper->getInitialPreviewConfig($this->previewFiles);
	}

}

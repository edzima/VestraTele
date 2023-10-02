<?php

namespace common\modules\file\widgets;

use common\helpers\Url;
use common\modules\file\models\File;
use kartik\file\FileInput as BaseFileInput;
use yii\helpers\Html;

class FileInput extends BaseFileInput {

	public array $previewRoute = [];
	public array $deleteRoute = [];

	public string $paramFileId = 'fileId';

	/**
	 * @var File[]
	 */
	public array $previewFiles = [];

	public function init() {
		parent::init();
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
		$initialPreview = [];
		foreach ($this->previewFiles as $file) {
			if (substr($file->mime, 0, 5) === 'image') {
				$url = $this->previewRoute;
				$url[$this->paramFileId] = $file->id;
				$initialPreview[] = Html::img($url, ['class' => 'file-preview-image']);
			} else {
				$initialPreview[] = Html::beginTag('div', ['class' => 'file-preview-other']) .
					Html::beginTag('h2') .
					Html::tag('i', '', ['class' => 'glyphicon glyphicon-file']) .
					Html::endTag('h2') .
					Html::endTag('div');
			}
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
				'url' => Url::to($url),
			];
		}
		return $initialPreviewConfig;
	}
}

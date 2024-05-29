<?php

namespace common\modules\file\widgets;

use common\helpers\Html;
use common\modules\file\models\FileType;
use common\widgets\ButtonDropdown;

class IssueFileUploadButton extends ButtonDropdown {

	public int $issueId;

	public $options = [
		'class' => 'btn btn-success',
	];

	public function init(): void {
		if ($this->label === 'Button') {
			$this->label = Html::icon('upload');
			$this->encodeLabel = false;
		}
		if (!isset($this->dropdown['items'])) {
			$this->dropdown['items'] = $this->defaultItems();
		}
		$this->options['id'] = $this->getId();
		parent::init();
	}

	public function run(): string {
		if (!isset($this->dropdown['items'])) {
			return '';
		}
		return parent::run();
	}

	protected function defaultItems(): array {
		$types = FileType::getNames(true);

		$items = [];
		foreach ($types as $typeId => $name) {
			$items[] = [
				'label' => $name,
				'url' => ['/file/issue/upload', 'issue_id' => $this->issueId, 'file_type_id' => $typeId],
			];
		}
		return $items;
	}
}

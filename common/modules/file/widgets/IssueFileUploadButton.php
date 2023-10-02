<?php

namespace common\modules\file\widgets;

use common\helpers\Html;
use common\modules\file\models\FileType;
use common\widgets\ButtonDropdown;

class IssueFileUploadButton extends ButtonDropdown {

	public int $issueId;

	public function init(): void {
		if ($this->label === 'Button') {
			$this->label = Html::icon('upload');
			$this->encodeLabel = false;
		}
		if (!isset($this->dropdown['items'])) {
			$this->dropdown['items'] = $this->defaultItems();
		}
		$this->options['id'] = $this->getId();
	}

	public function run(): string {
		if (!isset($this->dropdown['items'])) {
			return '';
		}
		return parent::run();
	}

	protected function defaultItems(): array {
		/** @var FileType[] $types */
		$types = FileType::find()
			->andWhere(['is_active' => true])
			->orderBy(['name' => SORT_ASC])
			->all();

		$items = [];
		foreach ($types as $type) {
			$items[] = [
				'label' => $type->name,
				'url' => ['/file/issue/upload', 'issue_id' => $this->issueId, 'file_type_id' => $type->id],
			];
		}
		return $items;
	}
}

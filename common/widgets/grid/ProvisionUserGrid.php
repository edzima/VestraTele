<?php

namespace common\widgets\grid;

use common\widgets\GridView;

class ProvisionUserGrid extends GridView {

	public $summary  = '';

	public function init(): void {
		if (empty($this->columns)) {
			$this->columns = $this->defaultColumns();
		}
		parent::init();
	}

	public function defaultColumns(): array {
		return [
			'toUser',
			'fromUserString',
			'value:currency',
		];
	}
}

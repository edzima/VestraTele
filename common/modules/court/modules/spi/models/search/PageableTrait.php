<?php

namespace common\modules\court\modules\spi\models\search;

trait PageableTrait {

	public $page;
	public $size;

	protected function pageableRules(): array {
		return [
			[['page', 'size'], 'integer', 'min' => 0],
		];
	}

	protected function pageableParams(): array {
		return [
			'page' => $this->page,
			'size' => $this->size,
		];
	}

	protected function loadPageableParams(array $params): void {
		if (isset($params['page'])) {
			$this->page = $params['page'];
		}
		if (isset($params['size'])) {
			$this->size = $params['size'];
		}
	}
}

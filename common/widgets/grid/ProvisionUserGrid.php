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
			'type.name',
			'toUser',//@todo add link to provision user schema for User::PERMISSION_PROVISION
			'fromUserString',//@todo add link to provision user schema for User::PERMISSION_PROVISION
			'provision',
			'value:currency',
		];
	}
}

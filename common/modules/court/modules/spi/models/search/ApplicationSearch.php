<?php

namespace common\modules\court\modules\spi\models\search;

use common\modules\court\modules\spi\repository\ApplicationsRepository;

class ApplicationSearch extends SearchModel {

	public string $court = '';
	public ?string $department = '';

	public function __construct(ApplicationsRepository $repository, string $appeal, array $config = []) {
		parent::__construct($repository, $appeal, $config);
	}

	public function rules(): array {
		return [
			[['court', 'department'], 'string'],
		];
	}

	public function getApiParams(): array {
		return [
			'courtName.contains' => $this->court,
			'department.contains' => $this->department,
		];
	}

}

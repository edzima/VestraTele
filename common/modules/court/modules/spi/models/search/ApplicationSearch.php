<?php

namespace common\modules\court\modules\spi\models\search;

use common\modules\court\modules\spi\components\SPIApi;
use common\modules\court\modules\spi\models\application\ApplicationViewDTO;
use yii\data\DataProviderInterface;

class ApplicationSearch extends ApplicationViewDTO {

	use PageableTrait;

	public string $court = '';
	public ?string $department = '';

	public function rules(): array {
		return array_merge($this->pageableRules(), [
				[['court', 'department'], 'string'],
			]
		);
	}

	public function search(SPIApi $api, array $params = []): ?DataProviderInterface {
		$this->load($params);
		$this->loadPageableParams($params);
		if ($this->validate()) {
			return $api->getApplications($this->getApiParams());
		}
		return null;
	}

	private function getApiParams(): array {
		$params = [
			'courtName.contains' => $this->court,
			'department.contains' => $this->department,
		];
		$params += $this->pageableParams();
		return array_filter($params, function ($value) {
			return !empty($value);
		});
	}

}

<?php

namespace common\modules\court\components;

use common\helpers\ArrayHelper;
use yii\base\Component;
use yii\helpers\Json;

class CourtApi extends Component {

	public string $apiUrl = 'https://api.dane.gov.pl/1.4/resources/51607,dane-teleadresowe-sadow?lang=pl';

	private const KEY_LAST_UPDATE_DATE = 'data.attributes.data_date';
	private const KEY_FILE_URL = 'data.attributes.file_url';

	private array $apiData = [];

	public function getFileUrl(): string {
		return ArrayHelper::getValue($this->getApiData(), static::KEY_FILE_URL);
	}

	public function getLastUpdateDate(): string {
		return ArrayHelper::getValue($this->getApiData(), static::KEY_LAST_UPDATE_DATE);
	}

	public function getApiData(): array {
		if (empty($this->apiData)) {
			$this->apiData = Json::decode(file_get_contents($this->apiUrl));
		}
		return $this->apiData;
	}
}

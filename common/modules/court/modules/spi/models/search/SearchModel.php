<?php

namespace common\modules\court\modules\spi\models\search;

use common\modules\court\modules\spi\components\SPIApi;
use yii\base\Model;

class SearchModel extends Model implements PageableInterface {

	protected SPIApi $api;

	protected int $page;
	protected int $size;
	protected string $sort;

	public function __construct(SPIApi $api, array $config = []) {
		$this->api = $api;
		parent::__construct($config);
	}

	public function getPage(): int {
		return $this->page;
	}

	public function getSize(): int {
		return $this->size;
	}

	public function getSort(): string {
		return $this->sort;
	}

}

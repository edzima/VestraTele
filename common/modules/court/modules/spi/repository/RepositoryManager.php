<?php

namespace common\modules\court\modules\spi\repository;

use common\modules\court\modules\spi\components\SPIApi;
use common\modules\court\modules\spi\Module;
use Yii;
use yii\base\Component;
use yii\di\Instance;

class RepositoryManager extends Component {

	public $api = 'spiApi';

	public $lawsuit = [
		'class' => LawsuitRepository::class,
	];

	public function init(): void {
		$this->api = Instance::ensure(
			$this->api,
			SPIApi::class,
			Module::getInstance()
		);
		parent::init();
	}

	public function getLawsuit(): LawsuitRepository {
		return Yii::createObject($this->lawsuit, [$this->api]);
	}

}

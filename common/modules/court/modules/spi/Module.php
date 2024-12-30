<?php

namespace common\modules\court\modules\spi;

use common\modules\court\modules\spi\components\SPIApi;
use Yii;
use yii\base\Module as BaseModule;

class Module extends BaseModule {

	public $controllerNamespace = 'common\modules\court\modules\spi\controllers';

	public $spiApi = [
		'class' => SpiApi::class,
	];

	public function init(): void {
		parent::init();
		$this->setComponents([
			'spiApi' => $this->spiApi,
		]);
		$this->registerTranslations();
		Yii::setAlias('@edzima/spi', __DIR__);
	}

	public function registerTranslations(): void {
		Yii::$app->i18n->translations['edzima/spi/*'] = [
			'class' => 'yii\i18n\PhpMessageSource',
			'sourceLanguage' => 'en-US',
			'basePath' => '@edzima/spi/messages',
			'fileMap' => [
				'edzima/spi/lawsuit' => 'lawsuit.php',
			],
		];
	}

	public function getSpiApi(): SPIApi {
		return $this->get('spiApi');
	}

	/**
	 * Module wrapper for `Yii::t()` method.
	 *
	 * @param string $message
	 * @param array $params
	 * @param null|string $language
	 *
	 * @return string
	 */
	public static function t(string $category, string $message, array $params = [], ?string $language = null) {
		return Yii::t('edzima/spi/' . $category, $message, $params, $language);
	}

}

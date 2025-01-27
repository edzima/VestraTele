<?php

namespace common\modules\court\modules\spi;

use common\modules\court\modules\spi\components\SPIApi;
use common\modules\court\modules\spi\entity\AppealInterface;
use common\modules\court\modules\spi\models\SpiUserAuth;
use common\modules\court\modules\spi\repository\RepositoryManager;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Module as BaseModule;

class Module extends BaseModule implements AppealInterface {

	public const PERMISSION_SPI_USER_AUTH = 'spi.user-auth';
	public const PERMISSION_SPI_LAWSUIT_DETAIL = 'spi.lawsuit.detail';

	public $controllerNamespace = 'common\modules\court\modules\spi\controllers';

	public ?string $appeal = null;

	public string $appealParamName = 'appeal';
	public string $userAuthApiPasswordKey;

	public bool $bindUserAuth = true;

	/**
	 * @var string|array|SPIApi
	 */
	public $spiApi = [
		'class' => SpiApi::class,
	];

	/**
	 * @var string|array|RepositoryManager
	 */
	public $repositoryManager = [
		'class' => RepositoryManager::class,
	];

	public static function getAppealsNames(): array {
		return [
			AppealInterface::APPEAL_BIALYSTOK => static::t('appeal', 'Bialystok'),
			AppealInterface::APPEAL_GDANSK => static::t('appeal', 'Gdansk'),
			AppealInterface::APPEAL_KATOWICE => static::t('appeal', 'Katowice'),
			AppealInterface::APPEAL_KRAKOW => static::t('appeal', 'Krakow'),
			AppealInterface::APPEAL_LUBLIN => static::t('appeal', 'Lublin'),
			AppealInterface::APPEAL_LODZ => static::t('appeal', 'Lodz'),
			AppealInterface::APPEAL_POZNAN => static::t('appeal', 'Poznan'),
			AppealInterface::APPEAL_RZESZOW => static::t('appeal', 'Rzeszow'),
			AppealInterface::APPEAL_SZCZECIN => static::t('appeal', 'Szczecin'),
			AppealInterface::APPEAL_WARSZAWA => static::t('appeal', 'Warszawa'),
			AppealInterface::APPEAL_WROCLAW => static::t('appeal', 'Wroclaw'),
		];
	}

	public function init(): void {
		parent::init();
		$this->setApiComponent();
		$this->setRepositoryComponent();
		$this->registerTranslations();
		Yii::setAlias('@edzima/spi', __DIR__);
	}

	public function getAppeal(): string {
		if ($this->appeal === null) {
			$appeal = Yii::$app->request->get(
				$this->appealParamName,
			);
			if ($appeal === null) {
				$appeal = $this->getSpiApi()->getAppeal();
			}
			$this->appeal = $appeal;
		}
		return $this->appeal;
	}

	public function registerTranslations(): void {
		Yii::$app->i18n->translations['edzima/spi/*'] = [
			'class' => 'yii\i18n\PhpMessageSource',
			'sourceLanguage' => 'en-US',
			'basePath' => '@edzima/spi/messages',
			'fileMap' => [
				'edzima/spi/appeal' => 'appeal.php',
				'edzima/spi/application' => 'application.php',
				'edzima/spi/lawsuit' => 'lawsuit.php',
				'edzima/spi/notification' => 'notification.php',
			],
		];
	}

	public function getSpiApi(int $userId = null): SPIApi {
		/* @var SPIApi $api */
		$api = $this->get('spiApi');
		if ($this->bindUserAuth) {
			$model = $this->findUserAuth($userId);
			if ($model === null) {
				throw new InvalidConfigException('Not found Auth Setting for User: ' . $userId);
			}
			$api->username = $model->username;
			$api->password = $model->decryptPassword($this->userAuthApiPasswordKey);
			$api->on(SPIApi::EVENT_AFTER_REQUEST, function ($event) use ($model) {
				$model->touchLastActionAt();
			});
		}
		return $this->get('spiApi');
	}

	public function getRepositoryManager(): RepositoryManager {
		$manager = $this->get('repositoryManager');
		$manager->appeal = $this->getAppeal();
		return $manager;
	}

	protected function setApiComponent(): void {
		$api = $this->spiApi;
		if (is_array($api) && !isset($api['class'])) {
			$api['class'] = SPIApi::class;
		}
		$this->setComponents([
			'spiApi' => $api,
		]);
	}

	protected function setRepositoryComponent(): void {
		$manager = $this->repositoryManager;
		if (is_array($manager) && !isset($manager['class'])) {
			$manager['class'] = RepositoryManager::class;
		}
		$this->setComponents([
			'repositoryManager' => $manager,
		]);
	}

	private function findUserAuth(?int $userId): ?SpiUserAuth {
		if ($userId === null) {
			if (Yii::$app->has('user')) {
				$userId = Yii::$app->user->getId();
			}
		}
		return $userId ? SpiUserAuth::findByUserId($userId) : null;
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

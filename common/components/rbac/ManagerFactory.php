<?php

namespace common\components\rbac;

use common\models\settlement\CostType;
use common\models\settlement\SettlementType;
use Yii;
use yii\base\Component;

class ManagerFactory extends Component {

	public const FRONTEND_APP = ModelAccessManager::APP_FRONTEND;
	public const BACKEND_APP = ModelAccessManager::APP_BACKEND;

	public array $mapAppsIds = [
		self::BACKEND_APP => 'app-backend',
		self::FRONTEND_APP => 'app-frontend',
	];

	public string $defaultClass = ModelAccessManager::class;
	public ?string $appId = null;

	public array $managers = [
		SettlementType::class => [
			'class' => SettlementTypeAccessManager::class,
		],
		CostType::class => [
			'class' => CostTypeAccessManager::class,
		],
	];

	public function init(): void {
		parent::init();
		if (empty($this->appId)) {
			$this->appId = Yii::$app->id;
		}
	}

	public function getManager(string $modelClass): ?ModelAccessManager {
		if (!isset($this->managers[$modelClass])) {
			return null;
		}
		$config = $this->managers[$modelClass] ?? [];
		if (!isset($config['class'])) {
			$config['class'] = $this->defaultClass;
		}
		/**
		 * @var $manager ModelAccessManager
		 */
		$manager = Yii::createObject($config);
		$actions = $this->mapActions($manager->getAppsActions());
		$manager->setAppsActions($actions);
		$manager->setApp($this->appId);
		return $manager;
	}

	protected function mapActions(array $appsActions): array {
		$map = $appsActions;
		foreach ($this->mapAppsIds as $old => $new) {
			if (isset($map[$old])) {
				$values = $map[$old];
				unset($map[$old]);
				$map[$new] = $values;
			}
		}
		return $map;
	}

}

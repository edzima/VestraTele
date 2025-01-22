<?php

namespace common\tests\unit\court\spi;

use common\modules\court\modules\spi\components\SPIApi;
use common\modules\court\modules\spi\Module;
use common\modules\court\modules\spi\repository\LawsuitRepository;
use common\modules\court\modules\spi\repository\RepositoryManager;
use common\tests\unit\Unit;
use Yii;
use yii\di\NotInstantiableException;

class RepositoryManagerTest extends Unit {

	public function testInitWithoutLoadModuleAndApi() {
		$this->tester->expectThrowable(NotInstantiableException::class, function () {
			new RepositoryManager();
		});
	}

	public function testBindDirectApi(): void {
		$manager = new RepositoryManager([
			'api' => SPIApi::testApi(),
		]);
		$this->tester->assertInstanceOf(SPIApi::class, $manager->api);
	}

	public function testCreateWithModuleWithoutApi() {
		Yii::$app->setModule('spi', [
			'class' => Module::class,
		]);
		Yii::$app->getModule('spi');
		$manager = new RepositoryManager();
		$this->tester->assertInstanceOf(SPIApi::class, $manager->api);
	}

	public function testCreateLawsuit() {
		$manager = new RepositoryManager([
			'api' => SPIApi::testApi(),
		]);
		$lawsuit = $manager->getLawsuits();
		$this->tester->assertInstanceOf(LawsuitRepository::class, $lawsuit);
	}

	public function testGetFromModule() {
		Yii::$app->setModule('spi', [
			'class' => Module::class,
		]);
		/**
		 * @var Module $module
		 */
		$module = Yii::$app->getModule('spi');
		$this->tester->assertInstanceOf(RepositoryManager::class, $module->getRepositoryManager());
	}

}

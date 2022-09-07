<?php

namespace common\tests\unit;

use common\components\keyStorage\FormModel;
use common\fixtures\KeyStorageFixture;
use common\models\KeyStorageItem;
use common\tests\_support\UnitModelTrait;
use yii\base\Model;
use yii\helpers\Json;

class KeyStorageFormModelTest extends Unit {

	use UnitModelTrait;

	private FormModel $model;

	public function _fixtures(): array {
		return [
			'key' => [
				'class' => KeyStorageFixture::class,
				'dataFile' => codecept_data_dir() . 'key-storage.php',
			],
		];
	}

	public function testKeyValueFromFixture(): void {
		$this->giveModel();

		$attribute = FormModel::attributeName(KeyStorageItem::KEY_FRONTEND_REGISTRATION);
		$this->tester->assertTrue((bool) $this->model->{$attribute});

		$attribute = FormModel::attributeName(KeyStorageItem::KEY_BACKEND_THEME_SKIN);
		$this->tester->assertSame('skin-blue', $this->model->{$attribute});
	}

	public function testSaveNewSingleItem(): void {
		$this->giveModel([
			'keys' => [
				'new-key' => [
					'type' => FormModel::TYPE_CHECKBOX,
				],
			],
		]);

		$this->model->setAttribute(FormModel::attributeName('new-key'), 'new-key-value');
		$this->thenSuccessSave();
		$this->tester->seeRecord(KeyStorageItem::class, [
			'key' => 'new-key',
			'value' => 'new-key-value',
		]);
	}

	public function testJson(): void {
		$this->giveModel([
			'keys' => [
				'list' => [
					'type' => FormModel::TYPE_CHECKBOXLIST,
					'json' => true,
				],
			],
		]);

		$this->model->setAttribute('list', [1, 3]);

		$this->thenSuccessSave();

		$this->tester->seeRecord(KeyStorageItem::class, [
			'key' => 'list',
			'value' => Json::encode([1, 3]),
		]);
	}

	private function giveModel(array $config = []): void {
		if (!isset($config['keys'])) {
			$config['keys'] = [
				KeyStorageItem::KEY_FRONTEND_REGISTRATION => [
					'type' => FormModel::TYPE_CHECKBOX,
				],
				KeyStorageItem::KEY_BACKEND_THEME_SKIN => [
					'type' => FormModel::TYPE_DROPDOWN,
					'items' => [
						'skin-blue' => 'skin-blue',
						'skin-black' => 'skin-black',
					],
				],
			];
		}
		$this->model = new FormModel($config);
	}

	public function getModel(): Model {
		return $this->model;
	}
}

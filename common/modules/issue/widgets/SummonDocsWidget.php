<?php

namespace common\modules\issue\widgets;

use common\helpers\Url;
use common\models\issue\SummonDocLink;
use yii\base\Widget;
use yii\data\ArrayDataProvider;
use yii\data\DataProviderInterface;

class SummonDocsWidget extends Widget {

	/**
	 * @var SummonDocLink[]
	 */
	public array $models = [];
	public $returnUrl;

	public function init() {
		parent::init();
		if (empty($this->returnUrl)) {
			$this->returnUrl = Url::current();
		}
	}

	public function run() {
		return $this->render('summon-docs', [
			'returnUrl' => $this->returnUrl,
			'toDoDataProvider' => $this->toDoDataProvider(),
			'toConfirmDataProvider' => $this->toConfirmDataProvider(),
			'confirmedDataProvider' => $this->confirmedDataProvider(),
		]);
	}

	private function toDoDataProvider(): DataProviderInterface {
		return $this->createDataProvider(
			array_filter($this->models,
				static function (SummonDocLink $model): bool {
					return $model->done_at === null;
				}
			)
		);
	}

	private function toConfirmDataProvider(): DataProviderInterface {
		return $this->createDataProvider(
			array_filter($this->models,
				static function (SummonDocLink $model): bool {
					return $model->done_at !== null && $model->confirmed_at === null;
				}
			)
		);
	}

	private function confirmedDataProvider(): DataProviderInterface {
		return $this->createDataProvider(
			array_filter($this->models,
				static function (SummonDocLink $model): bool {
					return $model->confirmed_at !== null;
				}
			)
		);
	}

	private function createDataProvider(array $models, array $config = []): DataProviderInterface {
		$config['allModels'] = $models;
		return new ArrayDataProvider($config);
	}
}

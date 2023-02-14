<?php

namespace common\modules\issue\widgets;

use common\helpers\Url;
use common\models\issue\Summon;
use common\models\issue\SummonDocLink;
use yii\base\Widget;
use yii\data\ArrayDataProvider;
use yii\data\DataProviderInterface;

class SummonDocsWidget extends Widget {

	/**
	 * @var SummonDocLink[]
	 */
	public array $models = [];
	public string $controller = '/summon-doc';
	public $returnUrl;

	/**
	 * @param Summon[] $summons
	 * @return SummonDocLink[]
	 */
	public static function modelsFromSummons(array $summons): array {
		$docs = [];
		foreach ($summons as $summon) {
			foreach ($summon->docsLink as $doc) {
				$docs[] = $doc;
			}
		}
		return $docs;
	}

	public function init() {
		parent::init();
		if (empty($this->returnUrl)) {
			$this->returnUrl = Url::current();
		}
	}

	public function run() {
		return $this->render('summon-docs', [
			'returnUrl' => $this->returnUrl,
			'controller' => $this->controller,
			'toDoDataProvider' => $this->toDoDataProvider(),
			'toConfirmDataProvider' => $this->toConfirmDataProvider(),
			'confirmedDataProvider' => $this->confirmedDataProvider(),
		]);
	}

	private function toDoDataProvider(): DataProviderInterface {
		return $this->createDataProvider(
			array_filter($this->models,
				static function (SummonDocLink $model): bool {
					return $model->isToDo();
				}
			)
		);
	}

	private function toConfirmDataProvider(): DataProviderInterface {
		return $this->createDataProvider(
			array_filter($this->models,
				static function (SummonDocLink $model): bool {
					return $model->isToConfirm();
				}
			)
		);
	}

	private function confirmedDataProvider(): DataProviderInterface {
		return $this->createDataProvider(
			array_filter($this->models,
				static function (SummonDocLink $model): bool {
					return $model->isConfirmed();
				}
			)
		);
	}

	private function createDataProvider(array $models, array $config = []): DataProviderInterface {
		$config['allModels'] = $models;
//		$config['keys'] = function (SummonDocLink $link){
//			return [
//				'summon_id' => $link->summon_id,
//				'doc_type_id' => $link->doc_type_id
//			];
//		};
		return new ArrayDataProvider($config);
	}
}

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
	public string $controller;
	public $returnUrl;

	public bool $hideOnAllAreConfirmed = false;

	/**
	 * @param Summon[] $summons
	 * @return SummonDocLink[]
	 */
	public static function modelsFromSummons(array $summons, int $userId = null): array {
		$docs = [];
		foreach ($summons as $summon) {
			if ($userId === null || $summon->isForUser($userId)) {
				foreach ($summon->docsLink as $doc) {
					$docs[] = $doc;
				}
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
		if (empty($this->models)) {
			return '';
		}
		$toDoDataProvider = $this->toDoDataProvider();
		$toConfirmDataProvider = $this->toConfirmDataProvider();
		if ($this->hideOnAllAreConfirmed
			&& empty($toDoDataProvider->getTotalCount())
			&& empty($toConfirmDataProvider->getTotalCount())) {
			return '';
		}
		return $this->render('summon-docs', [
			'dataProvider' => $this->createDataProvider($this->models),
			'returnUrl' => $this->returnUrl,
			'controller' => $this->controller,
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
		$this->sort($models);
		$config['allModels'] = $models;
		return new ArrayDataProvider($config);
	}

	private function sort(array &$models) {
		usort($models, static function (SummonDocLink $a, SummonDocLink $b) {
			return $a->doc->priority <=> $b->doc->priority;
		});
	}

}

<?php

namespace backend\modules\issue\widgets;

use backend\helpers\Html;
use common\models\issue\Summon;
use Yii;
use yii\base\Widget;
use yii\data\ArrayDataProvider;
use yii\data\DataProviderInterface;

class IssueViewSummonsWidgets extends Widget {

	public DataProviderInterface $dataProvider;

	public string $modelClass = Summon::class;

	public ?bool $withoutShowOnTop = true;

	public ?string $caption = null;

	public function init(): void {
		parent::init();
		if ($this->withoutShowOnTop === true) {
			$this->dataProvider = $this->createDataProvider(array_filter($this->dataProvider->getModels(), static function (Summon $model): bool {
				return !$model->type->getOptions()->showOnTop;
			}));
		}
		if ($this->caption === null) {
			$this->caption = $this->defaultCaption();
		}
	}

	protected function createDataProvider(array $models, array $config = []) {
		$config['allModels'] = $models;
		$config['key'] = 'id';
		if (!isset($config['modelClass'])) {
			$config['modelClass'] = $this->modelClass;
		}
		return new ArrayDataProvider($config);
	}

	private function defaultCaption(): string {
		$caption = Yii::t('issue', 'Summons');
		$modelsRealizedCount = $this->getRealizedDataProvider()->getCount();
		if ($modelsRealizedCount) {
			$caption .= Html::button(Yii::t('issue', 'Realized: {count}', [
				'count' => $modelsRealizedCount,
			]), [
				'class' => 'btn btn-success btn-sm pull-right',
				'data' => [
					'toggle' => 'collapse',
					'target' => '#' . $this->realizedId(),
				],
				'aria-expanded' => false,
				'aria-controls' => 'realizeSummons',
			]);
		}
		return $caption;
	}

	protected function getRealizedDataProvider(): DataProviderInterface {
		return $this->createDataProvider(array_filter($this->dataProvider->getModels(), static function (Summon $model) {
			return $model->isRealized();
		}));
	}

	protected function realizedId(): string {
		return $this->getId() . 'realizedSummons';
	}

	public function run() {
		if (empty($this->dataProvider->getModels())) {
			return '';
		}
		return $this->render('issue-view-summons', [
			'caption' => $this->caption,
			'withoutRealizedProvider' => $this->getWithoutRealizedProvider(),
			'realizedDataProvider' => $this->getRealizedDataProvider(),
			'realizedId' => $this->realizedId(),
		]);
	}

	protected function getWithoutRealizedProvider(): DataProviderInterface {
		return $this->createDataProvider(array_filter($this->dataProvider->getModels(), static function (Summon $model) {
			return !$model->isRealized();
		}));
	}
}

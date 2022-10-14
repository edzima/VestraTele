<?php

namespace backend\modules\issue\widgets;

use common\models\issue\Summon;
use yii\base\Widget;
use yii\data\ArrayDataProvider;
use yii\data\DataProviderInterface;

class IssueViewTopSummonsWidgets extends Widget {

	public DataProviderInterface $dataProvider;

	private array $typesDataProvider = [];

	public function init() {
		parent::init();

		$types = [];
		foreach ($this->dataProvider->getModels() as $model) {
			/**
			 * @var Summon $model
			 */
			if ($model->type->getOptions()->showOnTop) {
				$types[$model->type_id][$model->id] = $model;
			}
		}

		foreach ($types as $typeId => $models) {
			$this->typesDataProvider[$typeId] = new ArrayDataProvider([
				'models' => $models,
				'modelClass' => Summon::class,
			]);
		}
	}

	public function run(): string {
		return $this->render('issue-view-top-summons', [
			'typesDataProviders' => $this->typesDataProvider,
		]);
	}

}

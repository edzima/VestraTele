<?php

namespace common\modules\issue\widgets;

use yii\data\ActiveDataProvider;

/**
 * Widget for render Issue Summons.
 *
 * @property-read ActiveDataProvider $dataProvider
 */
class IssueSummonsWidget extends IssueWidget {

	public bool $addBtn = true;
	public bool $editBtn = true;
	public string $baseUrl = '/issue/summon/';
	public string $actionColumnTemplate = '{view} {update} {delete}';

	public function run(): string {
		$dataProvider = $this->getDataProvider();
		if ($dataProvider->getTotalCount() > 0) {
			return $this->render('issue-summons', [
				'model' => $this->model,
				'baseUrl' => $this->baseUrl,
				'dataProvider' => $dataProvider,
				'editBtn' => $this->editBtn,
				'addBtn' => $this->addBtn,
				'actionColumnTemplate' => $this->actionColumnTemplate,
			]);
		}
		return '';
	}

	public function getDataProvider(): ActiveDataProvider {
		$dataProvider = new ActiveDataProvider();
		$dataProvider->query = $this->model->getSummons();
		$dataProvider->sort = false;
		$dataProvider->pagination = false;
		return $dataProvider;
	}

}

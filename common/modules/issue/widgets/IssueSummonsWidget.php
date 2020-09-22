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
		return $this->render('issue-summons', [
			'model' => $this->model,
			'baseUrl' => $this->baseUrl,
			'dataProvider' => $this->getDataProvider(),
			'editBtn' => $this->editBtn,
			'addBtn' => $this->addBtn,
			'actionColumnTemplate' => $this->actionColumnTemplate,
		]);
	}

	public function getDataProvider(): ActiveDataProvider {
		$dataProvider = new ActiveDataProvider();
		$dataProvider->query = $this->model->getSummons();
		$dataProvider->sort = false;
		$dataProvider->pagination = false;
		return $dataProvider;
	}

}

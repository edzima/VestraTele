<?php

namespace common\modules\issue\widgets;

use backend\helpers\Url;
use backend\modules\issue\models\IssueStageChangeForm;
use common\models\issue\IssueInterface;
use Yii;
use yii\bootstrap\ButtonDropdown;

class StageChangeButtonDropdown extends ButtonDropdown {

	public string $route;
	public ?string $returnUrl = null;
	public $options = [
		'class' => 'btn-success',
	];
	public $tagName = 'a';
	public $split = true;
	public IssueInterface $model;

	public function init(): void {
		parent::init();
		if ($this->label === 'Button') {
			$this->label = Yii::t('issue', 'Change Stage');
		}
		if (!isset($this->dropdown['items'])) {
			$this->dropdown['items'] = $this->defaultItems();
		}
		if (!isset($this->options['href'])) {
			$this->options['href'] = [
				$this->route,
				'issueId' => $this->model->getIssueId(),
				'returnUrl' => $this->returnUrl,
			];
		}
	}

	public function defaultItems(): array {
		$stages = IssueStageChangeForm::getStagesNames($this->model->getIssueType()->id);
		unset($stages[$this->model->getIssueStage()->id]);
		$items = [];
		foreach ($stages as $id => $stage) {
			$items[] = [
				'label' => $stage,
				'linkOptions' => [
					'data-pjax' => 0,
				],
				'url' => Url::to([
					$this->route,
					'issueId' => $this->model->getIssueId(),
					'stageId' => $id,
					'returnUrl' => $this->returnUrl,
				]),
			];
		}
		return $items;
	}

}

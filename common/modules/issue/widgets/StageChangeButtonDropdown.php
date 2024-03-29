<?php

namespace common\modules\issue\widgets;

use backend\helpers\Url;
use backend\modules\issue\models\IssueStageChangeForm;
use common\helpers\Html;
use common\models\issue\IssueInterface;
use common\models\issue\IssueStage;
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
		$stages = IssueStageChangeForm::getStagesNames($this->model->getIssueTypeId());
		unset($stages[$this->model->getIssueStageId()]);
		asort($stages);
		$items = [];
		foreach ($stages as $id => $stage) {
			$stage = IssueStage::getStages()[$id];
			$label = Html::encode($stage->name) . Html::tag('strong', ' (' . Html::encode($stage->short_name) . ')');
			$items[] = [
				'label' => $label,
				'encode' => false,
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

<?php

namespace common\modules\lead\widgets;

use common\helpers\Html;
use common\modules\lead\models\LeadReport;
use common\modules\lead\Module;
use yii\base\Widget;

class LeadReportWidget extends Widget {

	public LeadReport $model;
	public bool $withDeleteButton = true;
	public ?bool $withUpdateButton = null;

	public ?bool $renderDeleted = null;

	public string $classDefault = 'panel-primary';
	public string $classChangeStatus = 'panel-success';
	public string $classPinned = 'panel-danger';
	public string $classDeleted = 'panel-deleted panel-transparent';

	public array $htmlOptions = [
		'class' => 'panel panel-note',
	];

	public function init() {
		parent::init();
		$this->initHtmlOptions();
		if ($this->renderDeleted === null) {
			$this->renderDeleted = !Module::manager()->onlyForUser;
		}
		if ($this->withUpdateButton === null) {
			$this->withUpdateButton = !Module::manager()->onlyForUser;
		}
	}

	public function run(): string {
		if ($this->model->isDeleted() && !$this->renderDeleted) {
			return '';
		}
		return $this->render('report', [
			'model' => $this->model,
			'htmlOptions' => $this->htmlOptions,
			'withDeleteButton' => $this->withDeleteButton,
			'withUpdateButton' => $this->withUpdateButton,
		]);
	}

	protected function initHtmlOptions(): void {
		$model = $this->model;

		if ($model->isDeleted()) {
			Html::addCssClass($this->htmlOptions, $this->classDeleted);
		}
		if ($model->is_pinned) {
			Html::addCssClass($this->htmlOptions, $this->classPinned);
		} else {
			if ($model->isChangeStatus()) {
				Html::addCssClass($this->htmlOptions, $this->classChangeStatus);
			} else {
				Html::addCssClass($this->htmlOptions, $this->classDefault);
			}
		}
	}

}

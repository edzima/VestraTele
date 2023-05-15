<?php

namespace common\modules\lead\widgets;

use common\modules\lead\models\LeadReport;
use common\modules\lead\Module;
use yii\base\Widget;

class LeadReportWidget extends Widget {

	public LeadReport $model;
	public bool $withDeleteButton = true;

	public ?bool $renderDeleted = null;

	public function init() {
		parent::init();
		if ($this->renderDeleted === null) {
			$this->renderDeleted = !Module::manager()->onlyForUser;
		}
	}

	public function run(): string {
		if ($this->model->isDeleted() && !$this->renderDeleted) {
			return '';
		}
		return $this->render('report', [
			'model' => $this->model,
			'withDeleteButton' => $this->withDeleteButton,
		]);
	}

}

<?php

namespace common\modules\issue\widgets;

use common\models\issue\form\IssueStageChangeForm;
use yii\base\Widget;

class IssueStageChangeWidget extends Widget {

	public IssueStageChangeForm $model;
	public ?string $noteDescriptionUrl;

	public function run(): string {
		return $this->render('issue-stage-change', [
			'model' => $this->model,
			'noteDescriptionUrl' => $this->noteDescriptionUrl,
		]);
	}
}

<?php

namespace common\modules\issue\widgets;

use common\helpers\Url;
use common\models\issue\IssueNoteForm;
use yii\base\Widget;

class IssueNoteFormWidget extends Widget {

	public array $options = [
		'id' => 'issue-note-form',
	];

	public IssueNoteForm $model;
	public array $titleListRoute = ['title-list'];
	public array $descriptionListRoute = ['description-list'];

	public function run(): string {
		return $this->render('issue-note_form', [
			'model' => $this->model,
			'options' => $this->options,
			'titleUrl' => Url::to($this->titleListRoute),
			'descriptionUrl' => Url::to($this->descriptionListRoute),
		]);
	}
}

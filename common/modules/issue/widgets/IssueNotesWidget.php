<?php

namespace common\modules\issue\widgets;

class IssueNotesWidget extends IssueWidget {

	public $addBtn = true;
	public $noteOptions = [];

	public function run() {
		return $this->render('issue-notes', [
			'model' => $this->model,
			'addBtn' => $this->addBtn,
			'noteOptions' => $this->noteOptions,
		]);
	}

}
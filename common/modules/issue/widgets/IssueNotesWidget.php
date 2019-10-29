<?php

namespace common\modules\issue\widgets;

use common\models\issue\IssueNote;

class IssueNotesWidget extends IssueWidget {

	public const TYPE_PAY = IssueNote::TYPE_PAY;

	public $addBtn = true;
	public $notes;
	public $type;
	/**
	 * @var IssueNote[]
	 */
	public $noteOptions = [];

	public function init() {
		parent::init();
		if ($this->notes === null) {
			$this->notes = $this->model->issueNotes;
		}
	}

	public function run() {
		return $this->render('issue-notes', [
			'model' => $this->model,
			'addBtn' => $this->addBtn,
			'noteOptions' => $this->noteOptions,
			'notes' => $this->notes,
			'type' => $this->type,
		]);
	}

}
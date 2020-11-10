<?php

namespace common\modules\issue\widgets;

use backend\helpers\Url;
use common\models\issue\IssueNote;

/**
 * Widget for render Issue Notes.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class IssueNotesWidget extends IssueWidget {

	public const TYPE_PAY = IssueNote::TYPE_PAY;
	public const TYPE_SUMMON = IssueNote::TYPE_SUMMON;

	public bool $addBtn = true;
	public ?string $addUrl = null;

	public ?array $notes = null;
	public ?string $type = null;
	/**
	 * @var IssueNote[]
	 */
	public array $noteOptions = [];

	/**
	 * {@inheritdoc}
	 */
	public function init(): void {
		parent::init();
		if ($this->notes === null) {
			$this->notes = $this->model->issueNotes;
		}
		if ($this->addBtn && $this->addUrl === null) {
			$this->addUrl = Url::to(['note/create', 'issueId' => $this->model->id]);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function run(): string {
		return $this->render('issue-notes', [
			'model' => $this->model,
			'addUrl' => $this->addUrl,
			'addBtn' => $this->addBtn,
			'noteOptions' => $this->noteOptions,
			'notes' => $this->notes,
			'type' => $this->type,
		]);
	}

}

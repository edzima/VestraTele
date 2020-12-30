<?php

namespace common\modules\issue\widgets;

use common\models\issue\IssueNote;
use yii\helpers\Url;

/**
 * Widget for render Issue Notes.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class IssueNotesWidget extends IssueWidget {

	public const TYPE_SETTLEMENT = IssueNote::TYPE_SETTLEMENT;
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
			$this->notes = $this->model
				->getIssueNotes()
				->withoutType()
				->joinWith('user.userProfile')
				->all();
		}
		if ($this->addBtn && $this->addUrl === null) {
			$this->addUrl = Url::to(['note/create', 'issueId' => $this->model->id]);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function run(): string {
		if (empty($this->notes)) {
			return '';
		}
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

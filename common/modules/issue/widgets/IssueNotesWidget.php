<?php

namespace common\modules\issue\widgets;

use common\models\issue\IssueNote;
use Yii;

/**
 * Widget for render Issue Notes.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class IssueNotesWidget extends IssueWidget {

	public const TYPE_SETTLEMENT = IssueNote::TYPE_SETTLEMENT;
	public const TYPE_SUMMON = IssueNote::TYPE_SUMMON;

	public bool $hideUserFront = false;

	public ?string $title = null;

	public ?array $notes = null;
	public ?string $type = null;

	/**
	 * @see IssueNoteWidget
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
				->withoutTypes([IssueNote::TYPE_SETTLEMENT])
				->orWhere(['type' => null])
				->joinWith('user.userProfile')
				->all();
		}
		if ($this->title === null) {
			$this->title = Yii::t('issue', 'Issue Notes');
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function run(): string {
		if (empty($this->notes)) {
			return '';
		}
		if ($this->hideUserFront) {
			$this->noteOptions['collapseTypes'] = [IssueNote::TYPE_USER_FRONT];
		}
		return $this->render('issue-notes', [
			'noteOptions' => $this->noteOptions,
			'notes' => $this->notes,
			'title' => $this->title,
			'id' => $this->getId(),
		]);
	}

}

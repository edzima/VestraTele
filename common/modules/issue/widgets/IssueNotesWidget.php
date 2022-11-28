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

	public const TYPE_USER_FRONT = IssueNote::TYPE_USER_FRONT;
	public const TYPE_SETTLEMENT = IssueNote::TYPE_SETTLEMENT;
	public const TYPE_SUMMON = IssueNote::TYPE_SUMMON;
	public const TYPE_SMS = IssueNote::TYPE_SMS;

	public ?string $title = null;

	public ?array $notes = null;
	public ?string $type = null;

	public array $collapseTypes = [];
	public bool $withProvisionControl = false;

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
			$query = $this->model
				->getIssueNotes()
				->joinWith('user.userProfile');

			if (!$this->withProvisionControl) {
				$query->withoutTypes([IssueNote::TYPE_SETTLEMENT_PROVISION_CONTROL]);
			}
			$this->notes = $query->all();
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
		$this->noteOptions['collapseTypes'] = $this->collapseTypes;
		return $this->render('issue-notes', [
			'noteOptions' => $this->noteOptions,
			'notes' => $this->notes,
			'title' => $this->title,
			'id' => $this->getId(),
		]);
	}

}

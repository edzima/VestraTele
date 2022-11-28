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

	public bool $withSettlements = true;

	public array $collapseTypes = [
		self::TYPE_SMS,
		self::TYPE_USER_FRONT,
	];

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
				->withoutTypes([IssueNote::TYPE_SETTLEMENT])
				->orWhere(['type' => null])
				->joinWith('user.userProfile');

			if (!$this->withSettlements) {
				$query->withoutTypes([IssueNote::TYPE_SETTLEMENT]);
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

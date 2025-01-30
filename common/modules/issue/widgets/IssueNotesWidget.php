<?php

namespace common\modules\issue\widgets;

use common\models\issue\IssueInterface;
use common\models\issue\IssueNote;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;

/**
 * Widget for render Issue Notes.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class IssueNotesWidget extends Widget {

	public ?IssueInterface $model = null;
	public const TYPE_USER_FRONT = IssueNote::TYPE_USER_FRONT;
	public const TYPE_SETTLEMENT = IssueNote::TYPE_SETTLEMENT;
	public const TYPE_SUMMON = IssueNote::TYPE_SUMMON;
	public const TYPE_SMS = IssueNote::TYPE_SMS;

	public ?string $title = null;

	public ?array $notes = null;
	public ?string $type = null;

	public array $collapseTypes = [];
	public bool $withProvisionControl = true;

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
			if ($this->model === null) {
				throw new InvalidConfigException('$model can not be empty.');
			}
			$query = IssueNote::find()
				->andWhere(['issue_id' => $this->model->getIssueId()])
				->orderBy(['publish_at' => SORT_DESC])
				->joinWith('user.userProfile')
				->joinWith('updater.userProfile');

			$linkedIds = $this->model->getLinkedIssuesIds();

			if (!empty($linkedIds)) {
				$ids = IssueNote::find()
					->select('id')
					->andWhere(['issue_id' => $linkedIds])
					->andWhere(['show_on_linked_issues' => ''])
					->orWhere(['like', 'CONCAT(CONCAT("|",show_on_linked_issues),"|")', '|' . $this->model->getIssueId() . '|'])
					->column();
				if (!empty($ids)) {
					$query->orWhere(['IN', IssueNote::tableName() . '.id', $ids]);
				}
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
			'issue_id' => $this->model ? $this->model->getIssueId() : null,
			'notes' => $this->notes,
			'title' => $this->title,
			'id' => $this->getId(),
		]);
	}

}

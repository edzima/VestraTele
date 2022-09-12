<?php

namespace common\models\issue;

use common\models\message\IssueNoteMessagesForm;
use common\models\user\User;
use Yii;
use yii\base\Model;

/**
 * Class IssueNoteForm
 *
 * @property IssueNote $note
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 *
 */
class IssueNoteForm extends Model {

	public const SCENARIO_STAGE_CHANGE = 'stage-change';
	public ?int $issue_id = null;
	public ?int $user_id = null;

	public ?string $type = null;
	public bool $is_pinned = false;
	public string $title = '';
	public ?string $description = null;
	public string $publish_at = '';

	public $linkedIssues = [];
	public bool $linkedIssuesMessages = true;

	public ?bool $stageChangeAtMerge = null;

	public string $dateFormat = 'Y-m-d H:i:s';

	public ?IssueNoteMessagesForm $messagesForm = null;

	private ?IssueInterface $issue = null;
	private ?IssueNote $model = null;

	public static function createSettlement(IssueSettlement $settlement) {
		$model = new static();
		$model->issue_id = $settlement->getIssueId();
		$model->type = IssueNote::generateType(IssueNote::TYPE_SETTLEMENT, $settlement->getId());
		return $model;
	}

	public static function createSummon(Summon $summon) {
		$model = new static();
		$model->type = IssueNote::generateType(IssueNote::TYPE_SUMMON, $summon->id);
		$model->issue_id = $summon->getIssueId();
		$model->title = $summon->getTitleWithDocs();
		return $model;
	}

	public function init(): void {
		parent::init();
		if (empty($this->publish_at)) {
			$this->publish_at = date($this->dateFormat);
		}
	}

	public function rules(): array {
		return [
			[['title', '!user_id', '!issue_id', 'publish_at'], 'required'],
			[['!title'], 'required', 'on' => static::SCENARIO_STAGE_CHANGE],
			[['stageChangeAtMerge'], 'required', 'on' => static::SCENARIO_STAGE_CHANGE],
			[['issue_id', 'user_id'], 'integer'],
			['is_pinned', 'boolean'],
			['!type', 'string'],
			[['title'], 'string', 'max' => 255],
			['description', 'string'],
			['description', 'default', 'value' => null],
			['publish_at', 'date', 'format' => 'php:' . $this->dateFormat],
			['issue_id', 'exist', 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
			['user_id', 'exist', 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
			['linkedIssuesMessages', 'boolean'],
			[
				'linkedIssues',
				'in',
				'range' => array_keys($this->getLinkedIssuesNames()),
				'when' => function (): bool {
					return !empty($this->getLinkedIssuesNames());
				},
				'allowArray' => true,
			],
		];
	}

	public function getLinkedIssuesNames(): array {
		if ($this->getIssue() === null) {
			return [];
		}
		$names = [];
		foreach ($this->getIssue()->getIssueModel()->linkedIssues as $issue) {
			$names[$issue->getIssueId()] = $issue->getIssueName() . ' - ' . $issue->customer;
		}
		return $names;
	}

	protected function getIssue(): ?IssueInterface {
		if ($this->issue === null || $this->issue_id !== $this->issue->getIssueId()) {
			$this->issue = Issue::findOne($this->issue_id);
		}
		return $this->issue;
	}

	public function attributeLabels(): array {
		return array_merge(
			IssueNote::instance()->attributeLabels(),
			[
				'stageChangeAtMerge' => Yii::t('issue', 'Stage change At merge'),
				'linkedIssues' => Yii::t('issue', 'Linked Issues'),
				'linkedIssuesMessages' => Yii::t('issue', 'Linked Issues Messages'),
			]
		);
	}

	public function validate($attributeNames = null, $clearErrors = true) {
		$validate = parent::validate($attributeNames, $clearErrors);
		if ($this->messagesForm) {
			$validate &= $this->messagesForm->validate();
		}
		return $validate;
	}

	public function load($data, $formName = null) {
		$load = parent::load($data, $formName);
		if ($this->messagesForm) {
			$load &= $this->messagesForm->load($data, $formName);
		}
		return $load;
	}

	public function pushMessages(): ?bool {
		if ($this->messagesForm) {
			$this->messagesForm->setNote($this->getModel());
			return $this->messagesForm->pushMessages();
		}
		return null;
	}

	public function setModel(IssueNote $model): void {
		$this->model = $model;
		$this->issue_id = $model->issue_id;
		$this->is_pinned = $model->is_pinned;
		$this->title = $model->title;
		$this->type = $model->type;
		$this->description = $model->description;
		$this->publish_at = (string) $model->publish_at;
		$this->user_id = $model->user_id;
		if ($model->isForStageChange()) {
			$this->scenario = static::SCENARIO_STAGE_CHANGE;
		}
	}

	public function getModel(): IssueNote {
		if ($this->model === null) {
			$this->model = new IssueNote();
		}
		return $this->model;
	}

	public function save(): bool {
		if ($this->beforeSave()) {
			$model = $this->getModel();
			$model->issue_id = $this->issue_id;
			$model->is_pinned = $this->is_pinned;
			$model->user_id = $this->user_id;
			$model->type = $this->type;
			$model->title = $this->title;
			$model->description = $this->description;
			$model->publish_at = $this->publish_at;
			$save = $model->save();
			if ($save) {
				$this->mergeStageChangeAt();
				$this->saveLinked();
				return $save;
			}
		}
		return false;
	}

	protected function mergeStageChangeAt(): void {
		if ($this->getModel()->isForStageChange() && $this->stageChangeAtMerge) {
			$issue = $this->getModel()->getIssueModel();
			$issue->stage_change_at = $this->publish_at;
			$issue->save();
		}
	}

	protected function beforeSave(): bool {
		return $this->validate();
	}

	private function saveLinked(): bool {
		if (!empty($this->linkedIssues)) {

			/**
			 * @var IssueInterface[] $issues
			 */
			$issues = array_filter($this->getIssue()->getIssueModel()->linkedIssues, function (IssueInterface $issue): bool {
				return in_array($issue->getIssueId(), (array) $this->linkedIssues);
			});
			foreach ($issues as $issue) {

				$model = new static();
				$model->setAttributes($this->getAttributes(null, [
					'linkedIssues',
					'issue_id',
				]), false);

				$model->issue_id = $issue->getIssueId();

				if ($model->save()) {
					if ($this->linkedIssuesMessages) {
						$model->pushMessages();
					}
				} else {
					Yii::warning(
						array_merge($model->getErrors(), [
							'issue_id' => $issue->getIssueId(),
						]), 'issue.stageChangeForm.linked');
				}
			}
		}
		return true;
	}

}

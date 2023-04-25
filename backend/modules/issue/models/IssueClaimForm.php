<?php

namespace backend\modules\issue\models;

use common\models\issue\Issue;
use common\models\issue\IssueClaim;
use common\models\issue\IssueInterface;
use common\models\issue\IssueTagType;
use common\models\issue\LinkedIssuesModel;
use Yii;
use yii\base\Model;

class IssueClaimForm extends Model implements LinkedIssuesModel {

	public const SCENARIO_TYPE = 'type';

	public string $type;

	public ?int $issue_id = null;

	public ?string $trying_value = null;
	public ?string $obtained_value = null;
	public ?string $percent_value = null;
	public ?string $details = null;
	public string $date = '';
	public ?int $entity_responsible_id = null;

	public $linkedIssuesIds = [];

	private ?IssueClaim $model = null;
	private ?IssueInterface $issue = null;

	public function getLinkedIssuesNames(): array {
		$issue = $this->getIssue();
		$names = [];
		if ($issue !== null) {
			foreach ($issue->getIssueModel()->linkedIssues as $linkedIssue) {
				$names[$linkedIssue->getIssueId()] = $this->getLinkedIssueName($linkedIssue);
			}
		}
		return $names;
	}

	public static function getEntityResponsibleNames(): array {
		return IssueClaim::getEntityResponsibleNames();
	}

	public static function getTypesNames(): array {
		return IssueClaim::getTypesNames();
	}

	public function rules(): array {
		return [
			[['issue_id', 'type', 'entity_responsible_id', 'date'], 'required'],
			[['!type'], 'required', 'on' => static::SCENARIO_TYPE],
			[['issue_id'], 'integer'],
			[['trying_value', 'obtained_value', 'percent_value'], 'number', 'min' => 0],
			[['trying_value', 'obtained_value', 'percent_value'], 'default', 'value' => null],
			[['type'], 'string'],
			[['details'], 'string', 'max' => 255],
			[['issue_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['issue_id' => 'id']],
			['type', 'in', 'range' => array_keys(static::getTypesNames())],
			['entity_responsible_id', 'in', 'range' => array_keys(static::getEntityResponsibleNames())],
			[
				'linkedIssuesIds',
				'in',
				'range' => array_keys($this->getLinkedIssuesNames()),
				'when' => function (): bool {
					return !empty($this->getLinkedIssuesNames());
				},
				'allowArray' => true,
			],
		];
	}

	public function attributeLabels(): array {
		return IssueClaim::instance()->attributeLabels() + [
				'linkedIssuesIds' => Yii::t('issue', 'Linked Issues'),
			];
	}

	public function isTypeScenario(): bool {
		return $this->scenario === static::SCENARIO_TYPE;
	}

	public function formName(): string {
		$name = parent::formName();
		if ($this->isTypeScenario()) {
			$name .= '-' . $this->type;
		}
		return $name;
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->date = $this->date;
		$model->details = $this->details;
		$model->entity_responsible_id = $this->entity_responsible_id;
		$model->issue_id = $this->issue_id;
		$model->obtained_value = $this->obtained_value;
		$model->percent_value = $this->percent_value;
		$model->trying_value = $this->trying_value;
		$model->type = $this->type;

		if ($model->save()) {
			$this->saveLinkedIssues();
			return true;
		}
		return false;
	}

	public function saveLinkedIssues(): ?int {
		$count = 0;

		foreach ($this->getLinkedIssuesIds() as $issueId) {

			$form = new static();
			$form->issue_id = $issueId;
			$model = IssueClaim::find()
				->andWhere([
					'type' => $this->type,
					'issue_id' => $issueId,
				])
				->one();
			if ($model !== null) {
				$form->setModel($model);
			}
			$form->setAttributes($this->getAttributes(null, [
				'linkedIssuesIds',
				'issue_id',
			]), false);
			if ($form->save(false)) {
				$count++;
			}
		}
		return $count;
	}

	public function getModel(): IssueClaim {
		if ($this->model === null) {
			$this->model = new IssueClaim();
		}
		return $this->model;
	}

	public function getIssue(): ?IssueInterface {
		if ($this->issue === null || $this->issue->getIssueId() !== $this->issue_id) {
			$this->issue = Issue::findOne($this->issue_id);
		}
		return $this->issue;
	}

	public function setModel(IssueClaim $model): void {
		$this->model = $model;
		$this->date = $model->date;
		$this->details = $model->details;
		$this->entity_responsible_id = $model->entity_responsible_id;
		$this->issue_id = $model->issue_id;
		$this->obtained_value = $model->obtained_value;
		$this->percent_value = $model->percent_value;
		$this->trying_value = $model->trying_value;
		$this->type = $model->type;
	}

	function getLinkedIssuesIds(): array {
		if (empty($this->linkedIssuesIds)) {
			return [];
		}
		return (array) $this->linkedIssuesIds;
	}

	private function getLinkedIssueName(IssueInterface $issue): string {
		$customerLinkedTags = IssueTagType::linkIssuesGridPositionFilter($issue->getIssueModel()->tags, IssueTagType::LINK_ISSUES_GRID_POSITION_COLUMN_CUSTOMER_BOTTOM);
		if (empty($customerLinkedTags)) {
			return strtr('{customer}:  {stage} - {issue}', [
				'{customer}' => $issue->getIssueModel()->customer,
				'{issue}' => $issue->getIssueName(),
				'{stage}' => $issue->getIssueStage()->name,
			]);
		}
		$tagsNames = [];
		foreach ($customerLinkedTags as $tag) {
			$tagsNames[] = $tag->name;
		}
		return strtr('{customer} ({tags}):  {stage} - {issue}', [
			'{customer}' => $issue->getIssueModel()->customer,
			'{issue}' => $issue->getIssueName(),
			'{stage}' => $issue->getIssueStage()->name,
			'{tags}' => implode(', ', $tagsNames),
		]);
	}
}

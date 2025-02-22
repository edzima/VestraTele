<?php

namespace common\modules\court\models;

use common\helpers\ArrayHelper;
use common\models\issue\Issue;
use common\models\issue\IssueInterface;
use Yii;
use yii\base\Model;

class LawsuitIssueForm extends Model {

	public int $creator_id;
	public $issuesIds = [];
	private ?Lawsuit $model = null;
	private ?IssueInterface $issue = null;
	public $court_id;

	public ?string $signature_act = '';
	public ?string $details = '';

	public ?string $due_at = '';
	public ?string $room = '';
	public ?string $location = null;

	public ?int $presence_of_the_claimant = null;

	public bool $is_appeal = false;
	public ?string $url = null;

	public function rules(): array {
		return [
			[['!creator_id', 'court_id', 'presence_of_the_claimant'], 'required'],
			[['creator_id', 'court_id', 'presence_of_the_claimant'], 'integer'],
			[['is_appeal'], 'boolean'],
			[['due_at', 'room', 'signature_act', 'details', 'location', 'url'], 'string'],
			['url', 'url'],
			[['due_at', 'room', 'signature_act', 'details', 'location'], 'default', 'value' => null],
			['location', 'in', 'range' => array_keys(static::getLocationNames())],
			['presence_of_the_claimant', 'in', 'range' => array_keys(static::getPresenceOfTheClaimantNames())],
			['issuesIds', 'exist', 'targetClass' => Issue::class, 'targetAttribute' => 'id', 'allowArray' => true],
		];
	}

	public function attributeLabels(): array {
		return array_merge(Lawsuit::instance()->attributeLabels(), [
			'issuesIds' => Yii::t('issue', 'Issues'),
		]);
	}

	public function setIssue(IssueInterface $issue): void {
		$this->issue = $issue;
		$this->issuesIds[] = $issue->getIssueId();
	}

	public function getLinkedIssuesNames(): array {
		if ($this->issue === null || empty($this->issue->getIssueModel()->linkedIssues)) {
			return [];
		}
		$linked = $this->issue->getIssueModel()->linkedIssues;
		$names = [];
		foreach ($linked as $issue) {
			$names[$issue->getIssueId()] = $issue->getIssueName() . ' - ' . $issue->customer->getFullName();
		}
		return $names;
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->presence_of_the_claimant = $this->presence_of_the_claimant;
		$model->creator_id = $this->creator_id;
		$model->court_id = $this->court_id;
		$model->due_at = $this->due_at;
		$model->room = $this->room;
		$model->details = $this->details;
		$model->signature_act = $this->signature_act;
		$model->location = $this->location;
		$model->is_appeal = $this->is_appeal;
		$model->url = $this->url;
		if (!$model->save(false)) {
			return false;
		}
		$this->linkIssues();
		return true;
	}

	protected function linkIssues(): void {
		$model = $this->getModel();
		$model->unlinkAll('issues');
		if ($this->issue) {
			if (empty($this->issuesIds)) {
				$this->issuesIds = [];
			}
			$this->issuesIds[] = $this->issue->getIssueId();
		}
		$model->linkIssues(array_unique($this->issuesIds));
	}

	public function setModel(Lawsuit $model) {
		$this->model = $model;
		$this->court_id = $model->court_id;
		$this->issuesIds = $model->getIssuesIds();
		$this->due_at = $model->due_at;
		$this->room = $model->room;
		$this->creator_id = $model->creator_id;
		$this->signature_act = $model->signature_act;
		$this->details = $model->details;
		$this->location = $model->location;
		$this->presence_of_the_claimant = $model->presence_of_the_claimant;
		$this->is_appeal = $model->is_appeal;
		$this->url = $model->url;
		if (count($model->issues) === 1) {
			$issues = $model->issues;
			$this->setIssue(reset($issues));
		}
	}

	public function getModel(): Lawsuit {
		if ($this->model === null) {
			$this->model = new Lawsuit();
		}
		return $this->model;
	}

	public function getCourtsNames(): array {
		return ArrayHelper::map(
			Court::find()
				->asArray()
				->select(['id', 'name'])
				->all(),
			'id', 'name'
		);
	}

	public static function getLocationNames(): array {
		return Lawsuit::getLocationNames();
	}

	public static function getPresenceOfTheClaimantNames(): array {
		return Lawsuit::getPresenceOfTheClaimantNames();
	}

}

<?php

namespace common\modules\court\models;

use common\helpers\ArrayHelper;
use common\models\issue\Issue;
use common\models\issue\IssueInterface;
use common\modules\court\modules\spi\components\LawsuitSignature;
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
	public bool $is_appeal = false;

	public string $signaturePattern = LawsuitSignature::DEFAULT_PATTERN;
	private ?Lawsuit $alreadyExistedLawsuit = null;

	public function rules(): array {
		return [
			[['!creator_id', 'court_id', 'signature_act'], 'required'],
			[['creator_id', 'court_id'], 'integer'],
			[['is_appeal'], 'boolean'],
			[['signature_act', 'details'], 'string'],
			[['signature_act'], 'trim'],
			['signature_act', 'match', 'pattern' => $this->signaturePattern],
			['signature_act', 'validateSignatureCourt'],
			['issuesIds', 'exist', 'targetClass' => Issue::class, 'targetAttribute' => 'id', 'allowArray' => true],
		];
	}

	public function validateSignatureCourt(): void {
		$query = Lawsuit::find()
			->andWhere(['court_id' => $this->court_id])
			->andWhere(['signature_act' => $this->signature_act]);
		if (!$this->getModel()->isNewRecord) {
			$query->andWhere(['not', ['id' => $this->getModel()->id]]);
		}
		$model = $query->one();
		if ($model !== null) {
			$this->addError('signature_act',
				Yii::t('court', 'Lawsuit: {signature_act} already exist in Court', [
					'signature_act' => $model->signature_act,
				])
			);
			$this->alreadyExistedLawsuit = $model;
		}
	}

	public function getAlreadyExistedLawsuit(): ?Lawsuit {
		return $this->alreadyExistedLawsuit;
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
		$model->creator_id = $this->creator_id;
		$model->court_id = $this->court_id;
		$model->details = $this->details;
		$model->signature_act = $this->signature_act;
		$model->is_appeal = $this->is_appeal;
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
		$this->creator_id = $model->creator_id;
		$this->signature_act = $model->signature_act;
		$this->details = $model->details;
		$this->is_appeal = $model->is_appeal;
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

	public function setCourtName(string $courtName): void {
		$names = static::getCourtsNames();
		foreach ($names as $id => $name) {
			if ($name === $courtName) {
				$this->court_id = $id;
				break;
			}
		}
	}

	public function getCourtName(): ?string {
		return static::getCourtsNames()[$this->court_id] ?? null;
	}

}

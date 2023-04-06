<?php

namespace backend\modules\issue\models;

use common\models\issue\Issue;
use common\models\issue\IssueClaim;
use common\models\issue\IssueInterface;
use yii\base\Model;

class IssueClaimForm extends Model {

	public const SCENARIO_TYPE = 'type';

	public string $type;

	public int $issue_id;

	public ?string $trying_value = null;
	public ?string $obtained_value = null;
	public ?string $percent_value = null;
	public ?string $details = null;
	public string $date = '';
	public ?int $entity_responsible_id = null;

	private ?IssueClaim $model = null;
	private ?IssueInterface $issue = null;

	public function getIssue(): ?IssueInterface {
		if ($this->issue === null || $this->issue->getIssueId() !== $this->issue_id) {
			$this->issue = Issue::findOne($this->issue_id);
		}
		return $this->issue;
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
		];
	}

	public function attributeLabels(): array {
		return IssueClaim::instance()->attributeLabels();
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
}

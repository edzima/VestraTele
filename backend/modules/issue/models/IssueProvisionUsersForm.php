<?php

namespace backend\modules\issue\models;

use common\models\issue\Issue;
use common\models\provision\ProvisionUser;
use common\models\User;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class IssueProvisionUsersForm extends Model {

	/* @var int */
	public $agentProvision;
	/* @var int */
	public $teleProvision;
	/* @var int */
	public $lawyerProvision;

	/**
	 * @var Issue
	 */
	private $issue;

	/**
	 * @var ProvisionUser[]
	 */
	private $agentProvisions = [];
	/**
	 * @var ProvisionUser[]
	 */
	private $lawyerProvisions = [];
	/**
	 * @var ProvisionUser[]
	 */
	private $teleProvisions = [];

	public function rules(): array {
		return [
			['teleProvision', 'required', 'when' => function () { return $this->isWithTele(); }],
			[['agentProvision', 'lawyerProvision'], 'required'],
			['agentProvision', 'in', 'range' => array_keys($this->getAgentOptions())],
			['teleProvision', 'in', 'range' => array_keys($this->getTeleOptions())],
			['lawyerProvision', 'in', 'range' => array_keys($this->getLawyerOptions())],
		];
	}

	public function attributeLabels(): array {
		return [
			'lawyerProvision' => 'Prawnik',
			'agentProvision' => 'Agent',
			'teleProvision' => 'Telemarketer',
		];
	}

	public function setIssue(Issue $issue): void {
		$this->issue = $issue;
		$provisions = Yii::$app->provisions->getIssueUsersProvisions($this->issue);
		$this->lawyerProvisions = $provisions['lawyer'];
		$this->agentProvisions = $provisions['agent'];
		$this->teleProvisions = $provisions['tele'];
	}

	public function isWithTele(): bool {
		return $this->issue->hasTele();
	}

	public function getLawyerOptions(): array {
		return $this->getOptions($this->lawyerProvisions);
	}

	public function getAgentOptions(): array {
		return $this->getOptions($this->agentProvisions);
	}

	public function getTeleOptions(): array {
		return $this->getOptions($this->teleProvisions);
	}

	/**
	 * @param ProvisionUser[] $provisions
	 * @return array
	 */
	private function getOptions(array $provisions): array {
		return ArrayHelper::map($provisions, 'type_id', 'typeWithValue');
	}

	public function getTele(): ?User {
		return $this->issue->tele;
	}

	public function getAgent(): User {
		return $this->issue->agent;
	}

	public function getLawyer(): User {
		return $this->issue->lawyer;
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		Yii::$app->provisions->removeForIssue($this->issue);

		if ($this->isWithTele()) {
			$this->addProvision($this->getTele(), $this->teleProvision);
		}
		$this->addProvision($this->getAgent(), $this->agentProvision);
		$this->addProvision($this->getLawyer(), $this->lawyerProvision);
		return true;
	}

	private function addProvision(User $user, int $type) {
		Yii::$app->provisions->add($user, $type, $this->issue->pays);
	}

}

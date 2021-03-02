<?php

namespace backend\modules\provision\models;

use common\models\issue\Issue;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueUser;
use common\models\provision\ProvisionType;
use common\models\provision\ProvisionUser;
use common\models\user\User;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class SettlementProvisionsForm extends Model {

	/* @var int */
	public $agentProvision;
	/* @var int */
	public $teleProvision;
	/* @var int */
	public $lawyerProvision;

	private IssuePayCalculation $model;

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

	public ?int $agentProvisionType = null;
	public ?int $lawyerProvisionType = null;
	public ?int $telemarketerProvisionType = null;

	private ?array $userTypes = null;

	public function __construct(IssuePayCalculation $model, $config = []) {
		$this->model = $model;
		parent::__construct($config);
	}

	public function init(): void {
		$provisions = Yii::$app->provisions->getIssueWorkersProvisions($this->getIssue());
		$this->lawyerProvisions = ArrayHelper::getValue($provisions, IssueUser::TYPE_LAWYER, []);
		$this->agentProvisions = ArrayHelper::getValue($provisions, IssueUser::TYPE_AGENT, []);
		$this->teleProvisions = ArrayHelper::getValue($provisions, IssueUser::TYPE_TELEMARKETER, []);
		parent::init();
	}

	/**
	 * @param string $issueUserType
	 * @return ProvisionType[]
	 */
	public function getUserTypes(string $issueUserType): array {
		return $this->getUsersTypes()[$issueUserType] ?? [];
	}

	public function getUsersTypes(): array {
		if ($this->userTypes === null) {
			$types = $this->getTypes();
			$userTypes = [];
			foreach ($this->getIssue()->users as $issueUser) {
				foreach ($types as $provisionType) {
					if ($provisionType->isForIssueUser($issueUser->type)) {
						$userSelf = ProvisionUser::find()
							->onlySelf($issueUser->user_id)
							->forType($provisionType->id)
							->all();
						if (empty($userSelf)) {
							$this->addError($this->getIssueUserAttributeName($issueUser->type),
								Yii::t('provision', 'Has not set self provision for type: {type}', [
									'user' => $issueUser->user->getFullName(),
									'type' => $provisionType->name,
								]));
						}
						$userTypes[$issueUser->type][$provisionType->id] = $provisionType;
					}
				}
			}
			$this->userTypes = $userTypes;
		}
		return $this->userTypes;
	}

	public function getIssueUserAttributeName(string $type): string {
		return $type . 'ProvisionType';
	}

	/**
	 * @return ProvisionType[]
	 */
	public function getTypes(): array {
		$allTypes = Yii::$app->provisions->getTypes();
		$types = [];
		foreach ($allTypes as $type) {
			if (
				$type->isForCalculationType($this->model->type)
				&& $type->isForIssueType($this->getIssue()->type_id)
			) {
				$types[] = $type;
			}
		}
		return $types;
	}

	public function rules(): array {
		return [
			['teleProvision', 'required', 'when' => function () { return $this->isWithTele(); }],
			[['agentProvision', 'lawyerProvision'], 'required'],
			['agentProvision', 'in', 'range' => array_keys($this->getAgentOptions())],
			['teleProvision', 'in', 'range' => array_keys($this->getTeleOptions())],
			['lawyerProvision', 'in', 'range' => array_keys($this->getLawyerOptions())],
		];
	}

	public function typeIsValid(ProvisionType $type): bool {

	}

	public function getAgentProvisions(): array {

	}

	public function attributeLabels(): array {
		return [
			'lawyerProvision' => 'Prawnik',
			'agentProvision' => 'Agent',
			'teleProvision' => 'Telemarketer',
		];
	}

	public function isWithTele(): bool {
		return $this->getIssue()->hasTele();
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
		return $this->getIssue()->tele;
	}

	public function getAgent(): User {
		return $this->getIssue()->agent;
	}

	public function getLawyer(): User {
		return $this->getIssue()->lawyer;
	}

	public function getModel(): IssuePayCalculation {
		return $this->model;
	}

	public function getIssue(): Issue {
		return $this->model->getIssueModel();
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		Yii::$app->provisions->removeForPays($this->model->getPays()->getIds());

		if ($this->isWithTele()) {
			$this->addProvision($this->getTele()->id, $this->teleProvision);
		}
		$this->addProvision($this->getAgent()->id, $this->agentProvision);
		$this->addProvision($this->getLawyer()->id, $this->lawyerProvision);
		return true;
	}

	private function addProvision(int $userId, int $type) {
		Yii::$app->provisions->add($userId, $type, $this->model->pays);
	}
}

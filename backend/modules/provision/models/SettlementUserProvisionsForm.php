<?php

namespace backend\modules\provision\models;

use common\models\issue\IssueCost;
use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueUser;
use common\models\provision\IssueProvisionType;
use common\models\provision\ProvisionUser;
use Decimal\Decimal;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class SettlementUserProvisionsForm extends Model {

	public ?int $typeId = null;
	public bool $costWithVAT = false;
	public bool $payWithVAT = false;

	private IssuePayCalculation $model;
	private IssueUser $user;
	/* @var IssueProvisionType[] */
	private ?array $types = null;

	private ProvisionUserData $provisionData;

	public function rules(): array {
		return [
			['typeId', 'required'],
			[['costWithVAT', 'costWithVAT'], 'boolean'],
			['typeId', 'in', 'range' => array_keys($this->getTypes())],
		];
	}

	/**
	 * SettlementUserProvisionsForm constructor.
	 *
	 * @param IssuePayCalculation $model
	 * @param string $type
	 * @param array $config
	 * @throws InvalidConfigException
	 */
	public function __construct(IssuePayCalculation $model, string $type, $config = []) {
		$this->model = $model;
		$this->user = $this->findUser($type);
		parent::__construct($config);
	}

	/**
	 * @param string $type
	 * @return IssueUser
	 * @throws InvalidConfigException
	 */
	protected function findUser(string $type): IssueUser {
		$user = $this->model->issue
			->getUsers()
			->withType($type)
			->with('user')
			->one();
		if ($user === null) {
			throw new InvalidConfigException('Issue User: ' . $type . ' not exist in issue: ' . $this->model->getIssueName() . '.');
		}
		return $user;
	}

	public function init() {
		$this->provisionData = $this->createProvisionData();
		parent::init();
	}

	private function createProvisionData(): ProvisionUserData {
		return new ProvisionUserData($this->user->user, ['date' => $this->model->issue->created_at]);
	}

	public function setType(IssueProvisionType $type): void {
		if (!$type->isForCalculation($this->model)) {
			throw new InvalidConfigException('Type is not valid for this settlement.');
		}
		$this->typeId = $type->id;
		$this->provisionData->type = $type;
	}

	public function getPaysSum(): Decimal {
		$sum = new Decimal(0);
		foreach ($this->model->pays as $pay) {
			$sum = $sum->add($this->getPayValue($pay));
		}
		return $sum;
	}

	public function getPaysSumWithoutGeneralCosts(): Decimal {
		return $this->getPaysSum()->sub($this->getGeneralCostsSum());
	}

	public function getProvisionsSum(ProvisionUser $provisionUser): Decimal {
		return $provisionUser->generateProvision($this->getPaysSumWithoutGeneralCosts());
	}

	public function getGeneralCostsSum(): Decimal {
		$sum = new Decimal(0);
		$costs = $this->model->getCostsWithoutUser();
		foreach ($costs as $cost) {
			$sum = $sum->add($this->getCostValue($cost));
		}
		return $sum;
	}

	public function getPersonalCostsSum(): Decimal {
		$sum = new Decimal(0);
		$costs = $this->model->getCostsWithUser($this->user->user_id);
		foreach ($costs as $cost) {
			$sum = $sum->add($this->getCostValue($cost));
		}
		return $sum;
	}

	public function getPayValue(IssuePay $pay): Decimal {
		return $this->payWithVAT ? $pay->getValueWithVAT() : $pay->getValueWithoutVAT();
	}

	public function getCostValue(IssueCost $cost): Decimal {
		return $this->costWithVAT ? $cost->getValueWithVAT() : $cost->getValueWithoutVAT();
	}

	public function getModel(): IssuePayCalculation {
		return $this->model;
	}

	public function getData(): ProvisionUserData {
		return $this->provisionData;
	}

	public function getIssueUser(): IssueUser {
		return $this->user;
	}

	/**
	 * @return IssueProvisionType[]
	 */
	public function getTypes(): array {
		if ($this->types === null) {
			$this->types = IssueProvisionType::findCalculationTypes($this->model, $this->user->type);
		}
		return $this->types;
	}

	public function getTypesNames(): array {
		return ArrayHelper::map($this->getTypes(), 'id', 'name');
	}

	public function save(): int {
		if (!$this->validate()) {
			return false;
		}
		$data = $this->provisionData;
		$data->type = $this->getTypes()[$this->typeId];
		return Yii::$app->provisions->addFromUserData($this->provisionData, $this->getPaysValues());
	}

	public function getPaysValues(): array {
		$pays = [];
		foreach ($this->model->pays as $pay) {
			$pays[$pay->id] = $this->getPayValue($pay);
		}
		return $pays;
	}
}

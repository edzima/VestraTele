<?php

namespace backend\modules\provision\models;

use common\models\issue\IssuePay;
use common\models\issue\IssuePayCalculation;
use common\models\issue\IssueUser;
use common\models\provision\IssueProvisionType;
use common\models\provision\ProvisionType;
use common\models\provision\ProvisionUser;
use Decimal\Decimal;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class SettlementUserProvisionsForm extends Model {

	public ?int $typeId = null;

	private IssuePayCalculation $model;
	private IssueUser $user;
	/* @var ProvisionType[] */
	private ?array $types = null;

	private ProvisionUserData $provisionData;

	public function __construct(IssuePayCalculation $model, string $type, $config = []) {
		$this->model = $model;
		$user = $model->issue->getUsers()->withType($type)->one();
		if ($user === null) {
			throw new InvalidConfigException('Issue User: ' . $type . ' not exist in issue: ' . $model->getIssueName() . '.');
		}
		$this->user = $user;
		parent::__construct($config);
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

	public function rules(): array {
		return [
			['typeId', 'required'],
			['typeId', 'in', 'range' => array_keys($this->getTypes())],
		];
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

	public function getPaysSum(): Decimal {
		$sum = new Decimal(0);
		foreach ($this->model->pays as $pay) {
			$sum = $sum->add($this->getPayValue($pay));
		}
		return $sum;
	}

	public function getProvisionsSum(ProvisionUser $provisionUser): Decimal {
		$sum = new Decimal(0);
		foreach ($this->model->pays as $pay) {
			$sum = $sum->add(Yii::$app->provisions->calculateProvision($provisionUser, $this->getPayValue($pay)));
		}
		return $sum;
	}

	public function getPayValue(IssuePay $pay): Decimal {
		return $pay->getValueWithoutVAT()->sub($pay->getCosts(false));
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
			$this->types = IssueProvisionType::findCalculationTypes($this->model, true, $this->user->type);
		}
		return $this->types;
	}

	public function getTypesNames(): array {
		return ArrayHelper::map($this->getTypes(), 'id', 'name');
	}

}

<?php

namespace common\models\provision;

use common\models\issue\IssuePayInterface;
use common\models\issue\IssueSettlement;
use common\models\issue\IssueUser;
use common\models\settlement\CostType;
use common\models\settlement\VATInfo;
use Decimal\Decimal;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\ArrayHelper;

class SettlementUserProvisionsForm extends Model {

	public ?int $typeId = null;
	public bool $costWithVAT = false;
	public bool $payWithVAT = false;

	public ?array $excludedIssueCostTypes = [];

	private IssueSettlement $model;
	private IssueUser $user;
	/* @var IssueProvisionType[] */
	private ?array $types = null;

	public function init(): void {
		parent::init();
		if (empty($this->excludedIssueCostTypes) && $this->excludedIssueCostTypes !== null) {
			$this->excludedIssueCostTypes = array_keys(
				array_filter(
					CostType::getModels(),
					function (CostType $costType) {
						return $costType->is_active && $costType->is_for_settlement;
					})
			);
		}
	}

	public function rules(): array {
		return [
			['typeId', 'required'],
			[['costWithVAT', 'costWithVAT'], 'boolean'],
			['typeId', 'in', 'range' => array_keys($this->getTypes())],
			['excludedIssueCostTypes', 'in', 'range' => array_keys(static::getCostTypesNames()), 'allowArray' => true],
		];
	}

	public function attributeLabels(): array {
		return [
			'typeId' => Yii::t('provision', 'Type'),
			'costWithVAT' => Yii::t('settlement', 'Cost With VAT'),
			'payWithVAT' => Yii::t('settlement', 'Pay With VAT'),
			'excludedIssueCostTypes' => Yii::t('settlement', 'Excluded Issue Costs Types'),
		];
	}

	/**
	 * SettlementUserProvisionsForm constructor.
	 * 49]=[\*
	 *
	 * @param IssueSettlement $model
	 * @param string $type
	 * @param array $config
	 * @throws InvalidConfigException
	 */
	public function __construct(IssueSettlement $model, string $type, $config = []) {
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
		$user = $this->model->getIssueModel()
			->getUsers()
			->withType($type)
			->with('user')
			->one();
		if ($user === null) {
			throw new InvalidConfigException('Issue User: ' . $type . ' not exist in issue: ' . $this->model->getIssueName() . '.');
		}
		return $user;
	}

	public function getModel(): IssueSettlement {
		return $this->model;
	}

	public function getData(): ProvisionUserData {
		$data = new ProvisionUserData($this->user->user);
		$data->date = $this->model->issue->created_at;
		if ($this->typeId) {
			$type = $this->getType($this->typeId);
			$data->type = $type;
			if ($type !== null && $type->getIsForDateFromSettlement()) {
				$data->date = $this->model->getCreatedAt();
			}
		}
		return $data;
	}

	public function getIssueUser(): IssueUser {
		return $this->user;
	}

	public function setType(IssueProvisionType $type): void {
		if (!$type->isForSettlement($this->model)) {
			throw new InvalidConfigException('Type is not valid for this settlement.');
		}
		$this->typeId = $type->id;
	}

	public function getType(int $id): ?IssueProvisionType {
		return $this->getTypes()[$id] ?? null;
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

	public function getGeneralCostsSum(bool $excludedTypes = false): Decimal {
		$sum = new Decimal(0);
		$costs = $this->model->getCostsWithoutUser($this->user->user_id);
		if ($excludedTypes && empty($this->excludedIssueCostTypes)) {
			$excludedTypes = false;
		}
		foreach ($costs as $cost) {
			if (!$excludedTypes || !in_array($cost->type_id, $this->excludedIssueCostTypes, true)) {
				$sum = $sum->add($this->getCostValue($cost));
			}
		}
		return $sum;
	}

	public function getProvisionsSum(ProvisionUser $provisionUser): Decimal {
		return $provisionUser->generateProvision($this->getPaysSumWithoutGeneralCosts());
	}

	public function getPayValue(IssuePayInterface $pay): Decimal {
		if ($this->model->type->is_percentage) {
			return $pay->getValue();
		}
		return $this->payWithVAT ? $pay->getValueWithVAT() : $pay->getValueWithoutVAT();
	}

	public function getCostValue(VATInfo $cost): Decimal {
		return $this->costWithVAT ? $cost->getValueWithVAT() : $cost->getValueWithoutVAT();
	}

	public function getTypesNames(): array {
		return ArrayHelper::map($this->getTypes(), 'id', 'name');
	}

	/**
	 * @return IssueProvisionType[]
	 */
	public function getTypes(): array {
		if ($this->types === null) {
			$types = IssueProvisionType::findSettlementTypes($this->model, $this->user->type);
			if (count($types) > 1) {
				$types = IssueProvisionType::filter($types, static function (IssueProvisionType $type): bool {
					return !empty($type->getIssueRequiredUserTypes());
				});
			}
			$this->types = $types;
		}
		return $this->types;
	}

	public function getPaysValues(): array {
		if (empty($this->model->pays)) {
			return [];
		}
		$pays = [];
		$costsSum = $this->getGeneralCostsSum(true);
		$subCosts = new Decimal(0);
		foreach ($this->model->pays as $pay) {
			$payValue = $this->getPayValue($pay);
			if ($subCosts < $costsSum) {
				if ($payValue > $costsSum) {

					$subCosts = $costsSum;
					$payValue = $payValue->sub($costsSum);
				} else {
					$subCosts = $subCosts->add($payValue);
					$payValue = new Decimal(0);
				}
			}
			if ($payValue > 0) {
				$pays[$pay->id] = $payValue;
			}
		}
		return $pays;
	}

	public function getIssueNotSettledUserCosts(): array {
		return $this->getModel()
			->getIssueModel()
			->getCosts()
			->notSettled()
			->withoutSettlements()
			->user($this->user->user_id)
			->all();
	}

	public function linkIssueNotSettledUserCosts(): int {
		$costs = $this->getIssueNotSettledUserCosts();
		if (empty($costs)) {
			return 0;
		}

		return $this->getModel()->linkCosts(ArrayHelper::getColumn($costs, 'id'));
	}

	/**
	 * @param IssueSettlement $model
	 * @param array $userTypes
	 * @param array $config
	 * @return static[]
	 * @throws InvalidConfigException
	 */
	public static function createModels(IssueSettlement $model, array $userTypes = [], array $config = []): array {
		if (empty($userTypes)) {
			$userTypes = ArrayHelper::getColumn($model->getIssueModel()->users, 'type');
		}
		$models = [];
		foreach ($userTypes as $userType) {
			$models[] = new static($model, $userType, $config);
		}
		return $models;
	}

	public static function getCostTypesNames(): array {
		return CostType::getNames(true);
	}

}

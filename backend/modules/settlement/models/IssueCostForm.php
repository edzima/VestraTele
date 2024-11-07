<?php

namespace backend\modules\settlement\models;

use common\models\forms\HiddenFieldsModel;
use common\models\issue\IssueCost;
use common\models\issue\IssueInterface;
use common\models\settlement\CostType;
use common\models\user\User;
use common\models\user\Worker;
use Decimal\Decimal;
use Yii;
use yii\base\Model;

/**
 * Form Model for IssueCost.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class IssueCostForm extends Model implements HiddenFieldsModel {

	public const SCENARIO_WITHOUT_ISSUE = 'without-issue';
	public const SCENARIO_SETTLE = 'settle';
	public bool $usersFromIssue = true;
	public string $value = '';
	public ?string $vat = null;

	public ?int $type_id = null;
	public ?string $transfer_type = null;
	public $user_id;
	public $creator_id;

	public string $date_at = '';
	public ?string $confirmed_at = null;
	public ?string $deadline_at = null;
	public ?string $settled_at = null;

	private ?IssueCost $model = null;
	private ?IssueInterface $issue = null;

	public function setIssue(?IssueInterface $issue) {
		$this->issue = $issue;
	}

	public static function createFromModel(IssueCost $cost): self {
		$model = new static();
		$model->setIssue($cost->issue);
		$model->setModel($cost);
		if ($model->user_id && $model->usersFromIssue && !isset($model->getUserNames()[$model->user_id])) {
			$model->usersFromIssue = false;
		}
		return $model;
	}

	public function attributeLabels(): array {

		return array_merge(
			IssueCost::instance()->attributeLabels(), [
			'value' => $this->vat ? Yii::t('settlement', 'Value with VAT') : Yii::t('settlement', 'Value'),
		]);
	}

	public function rules(): array {
		return [
			[['type_id', 'value', 'date_at'], 'required'],
			[['date_at', 'deadline_at', 'confirmed_at', 'settled_at'], 'date', 'format' => 'Y-m-d'],
			[
				'settled_at', 'compare', 'compareAttribute' => 'date_at', 'operator' => '>=',
				'enableClientValidation' => false,
			],
			[
				'user_id', 'required',
				'when' => function (): bool {
					if (empty($this->type_id) || !isset(CostType::getModels()[$this->type_id])) {
						return false;
					}
					return CostType::getModels()[$this->type_id]->getTypeOptions()->user_is_required ?? false;
				},
				'enableClientValidation' => false,
				'skipOnError' => true,
			],
			[['settled_at', 'transfer_type'], 'required', 'on' => static::SCENARIO_SETTLE],
			[['vat', 'settled_at', 'confirmed_at', 'deadline_at'], 'default', 'value' => null],
			[['user_id', 'creator_id'], 'integer'],
			[['value', 'vat'], 'number'],
			['vat', 'number', 'min' => 0, 'max' => 100],
			[['value'], 'number', 'min' => 1, 'max' => 100000],
			['type_id', 'in', 'range' => array_keys($this->getTypesNames())],
			['transfer_type', 'in', 'range' => array_keys(static::getTransfersTypesNames())],
			[
				'user_id',
				'in',
				'range' => array_keys($this->getUserNames()),
				'message' => Yii::t('backend', 'User must be from issue users.'),
				'except' => static::SCENARIO_WITHOUT_ISSUE,
			],

		];
	}

	public function getIssue(): ?IssueInterface {
		return $this->issue;
	}

	public function getModel(): IssueCost {
		if ($this->model === null) {
			$this->model = new IssueCost();
		}
		return $this->model;
	}

	public function setModel(IssueCost $cost): void {
		$this->model = $cost;
		$this->issue = $cost->issue;
		$this->type_id = $cost->type_id;
		$this->date_at = $cost->date_at;
		$this->confirmed_at = $cost->confirmed_at;
		$this->deadline_at = $cost->deadline_at;
		$this->settled_at = $cost->settled_at;
		$this->value = $cost->getValueWithVAT()->toFixed(2);
		$this->vat = $cost->getVAT() ? $cost->getVAT()->toFixed(2) : null;
		$this->user_id = $cost->user_id;
		$this->transfer_type = $cost->getTransferType();
		$this->creator_id = $cost->creator_id;
	}

	public function getTypesNames(): array {
		$names = CostType::getNames(true);
		if (!$this->getModel()->isNewRecord
			&& !isset($names[$this->type_id])) {
			$names[$this->type_id] = CostType::getNames(false)[$this->type_id];
		}
		codecept_debug($names);
		return $names;
	}

	public static function getTransfersTypesNames(): array {
		return IssueCost::getTransfersTypesNames();
	}

	public function getUserNames(): array {
		if (!$this->usersFromIssue || $this->getIssue() === null) {
			return User::getSelectList(Worker::getAssignmentIds([Worker::PERMISSION_ISSUE]), false);
		}
		return User::getSelectList(
			$this->getIssue()->getIssueModel()->getUsers()->select('user_id')->column(),
			false
		);
	}

	public function save(bool $validate = true): bool {
		if ($validate && !$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->issue_id = $this->getIssue() ? $this->getIssue()->getIssueId() : null;
		$model->value = $this->getValue()->toFixed(2);
		$model->vat = $this->vat ? (new Decimal($this->vat))->toFixed(2) : null;
		$model->transfer_type = $this->transfer_type;
		$model->type_id = $this->type_id;
		$model->confirmed_at = $this->confirmed_at;
		$model->date_at = $this->date_at;
		$model->user_id = $this->user_id;
		$model->settled_at = $this->settled_at;
		$model->deadline_at = $this->deadline_at;
		$model->creator_id = $this->creator_id;
		return $model->save(false);
	}

	public function isAttributeSafe($attribute): bool {
		return parent::isAttributeSafe($attribute) && $this->isVisibleField($attribute);
	}

	public function isVisibleField(string $attribute): bool {
		if ($this->scenario === static::SCENARIO_SETTLE) {
			$attributes = [
				'transfer_type',
				'settled_at',
			];
			return in_array($attribute, $attributes);
		}
		return true;
	}

	public function getValue(): Decimal {
		return new Decimal($this->value);
	}

}

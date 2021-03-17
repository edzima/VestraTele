<?php

namespace backend\modules\settlement\models;

use common\models\forms\HiddenFieldsModel;
use common\models\issue\Issue;
use common\models\issue\IssueCost;
use common\models\user\User;
use Decimal\Decimal;
use Yii;
use yii\base\Model;

/**
 * Form Model for IssueCost.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class IssueCostForm extends Model implements HiddenFieldsModel {

	public const SCENARIO_CREATE_INSTALLMENT = 'create-installment';

	public string $date_at = '';
	public ?string $settled_at = null;
	public string $type = '';
	public string $value = '';
	public string $vat = '';
	public $user_id;

	private ?IssueCost $model = null;
	private Issue $issue;

	public function __construct(Issue $issue, $config = []) {
		$this->issue = $issue;
		parent::__construct($config);
	}

	public function attributeLabels(): array {
		return [
			'date_at' => Yii::t('common', 'Date at'),
			'type' => Yii::t('common', 'Type'),
			'value' => Yii::t('common', 'Value with VAT'),
			'vat' => 'VAT (%)',
			'user_id' => Yii::t('common', 'User'),
			'settled_at' => Yii::t('common', 'Settled at'),
		];
	}

	public function rules(): array {
		return [
			[['type', 'value', 'vat', 'date_at'], 'required'],
			[['date_at', 'settled_at'], 'date', 'format' => 'Y-m-d'],
			[
				'settled_at', 'compare', 'compareAttribute' => 'date_at', 'operator' => '>=',
				'enableClientValidation' => false,
			],
			[
				'user_id', 'required',
				'when' => function (): bool {
					return $this->type === IssueCost::TYPE_INSTALLMENT;
				},
				'enableClientValidation' => false,
			],
			[
				'user_id', 'required',
				'on' => static::SCENARIO_CREATE_INSTALLMENT,
			],
			['user_id', 'integer'],
			[['value', 'vat'], 'number'],
			['vat', 'number', 'min' => 0, 'max' => 100],
			['value', 'number', 'min' => 1, 'max' => 10000],
			['type', 'in', 'range' => array_keys(static::getTypesNames())],
			['user_id', 'in', 'range' => array_keys($this->getUserNames()), 'message' => Yii::t('backend', 'User must be from issue users.')],
		];
	}

	public function getIssue(): Issue {
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
		$this->type = $cost->type;
		$this->date_at = $cost->date_at;
		$this->settled_at = $cost->settled_at;
		$this->value = $cost->getValueWithVAT()->toFixed(2);
		$this->vat = $cost->getVAT()->toFixed(2);
		$this->user_id = $cost->user_id;
	}

	public static function getTypesNames(): array {
		return IssueCost::getTypesNames();
	}

	public function getUserNames(): array {
		return User::getSelectList($this->issue->getUsers()->select('user_id')->column());
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$model = $this->getModel();
		$model->issue_id = $this->getIssue()->id;
		$model->value = (new Decimal($this->value))->toFixed(2);
		$model->vat = (new Decimal($this->vat))->toFixed(2);
		$model->type = $this->type;
		$model->date_at = $this->date_at;
		$model->user_id = $this->user_id;
		$model->settled_at = $this->settled_at;
		return $model->save(false);
	}

	public function isVisibleField(string $attribute): bool {
		if ($this->scenario === static::SCENARIO_CREATE_INSTALLMENT) {
			if ($attribute === 'type') {
				return false;
			}
			if ($attribute === 'settled_at') {
				return false;
			}
		}
		return true;
	}
}

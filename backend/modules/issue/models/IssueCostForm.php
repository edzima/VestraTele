<?php

namespace backend\modules\issue\models;

use common\models\issue\Issue;
use common\models\issue\IssueCost;
use Decimal\Decimal;
use Yii;
use yii\base\Model;

/**
 * Form Model for IssueCost.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class IssueCostForm extends Model {

	public string $date_at = '';
	public string $type = '';
	public string $value = '';
	public string $vat = '';

	private ?IssueCost $model = null;
	private Issue $issue;

	public function __construct(Issue $issue, $config = []) {
		$this->issue = $issue;
		parent::__construct($config);
	}

	public function attributeLabels(): array {
		return [
			'date_at' => Yii::t('backend', 'Date at'),
			'type' => Yii::t('backend', 'Type'),
			'value' => Yii::t('backend', 'Value with VAT'),
			'vat' => 'VAT (%)',
		];
	}

	public function rules(): array {
		return [
			[['type', 'value', 'vat', 'date_at'], 'required'],
			[['date_at'], 'date', 'format' => DATE_ATOM],
			[['value', 'vat'], 'number'],
			['vat', 'number', 'min' => 0, 'max' => 100],
			['value', 'number', 'min' => 1, 'max' => 10000],
			['type', 'in', 'range' => array_keys(static::getTypesNames())],
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
		$this->value = $cost->getValueWithVAT()->toFixed(2);
		$this->vat = $cost->getVAT()->toFixed(2);
	}

	public static function getTypesNames(): array {
		return IssueCost::getTypesNames();
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
		return $model->save(false);
	}
}

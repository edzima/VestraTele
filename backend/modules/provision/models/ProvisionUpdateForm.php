<?php

namespace backend\modules\provision\models;

use common\models\issue\Issue;
use common\models\issue\IssueInterface;
use common\models\provision\Provision;
use common\models\user\User;
use Decimal\Decimal;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;

/**
 * Class ProvisionForm
 *
 * @property-read int $id
 * @property-read User $toUser
 * @property-read Issue $issue
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class ProvisionUpdateForm extends Model {

	public $value;
	public $percent;
	public $hide_on_report;

	private Provision $model;

	public function __construct(Provision $model, $config = []) {
		if ($model->isNewRecord) {
			throw new InvalidConfigException('Provision can not be new record.');
		}
		$this->setModel($model);
		parent::__construct($config);
	}

	private function setModel(Provision $model): void {
		$this->model = $model;
		$this->value = $model->getValue()->toFixed(2);
		$this->percent = $model->getPercent() ? $model->getPercent()->toFixed(2) : null;
		$this->hide_on_report = $model->hide_on_report;
	}

	public function rules(): array {
		return [
			[['percent', 'value'], 'required'],
			['hide_on_report', 'boolean'],
			['percent', 'number', 'min' => 0, 'max' => 100],
			['value', 'number', 'min' => 0],
		];
	}

	public function attributeLabels(): array {
		return [
			'value' => Yii::t('provision', 'Provision ({currencySymbol})', ['currencySymbol' => Yii::$app->formatter->getCurrencySymbol()]),
			'percent' => Yii::t('provision', 'Provision (%)'),
			'hide_on_report' => Yii::t('provision', 'Hide on report'),
		];
	}

	public function getModel(): Provision {
		return $this->model;
	}

	public function getToUser(): User {
		return $this->model->toUser;
	}

	public function getIssue(): IssueInterface {
		return $this->model->pay->issue;
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$model = $this->model;
		$model->value = $this->generateValue()->toFixed(2);
		$model->hide_on_report = $this->hide_on_report;
		$model->percent = $this->generatePercent()->toFixed(2);
		return $model->save(false);
	}

	public function generatePercent(?Decimal $value = null): Decimal {
		if ($value === null) {
			$value = $this->model->getValue();
		}
		return $value->div(Yii::$app->provisions->issuePayValue($this->model->pay))->mul(100);
	}

	public function generateValue(): Decimal {
		$value = new Decimal($this->value);
		if (!$value->equals($this->getModel()->value)) {
			return $value;
		}
		$percent = new Decimal($this->percent);
		return Yii::$app->provisions->issuePayValue($this->model->pay)
			->mul($percent)
			->div(100);
	}
}

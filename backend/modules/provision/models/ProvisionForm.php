<?php

namespace backend\modules\provision\models;

use common\models\issue\Issue;
use common\models\issue\IssueInterface;
use common\models\provision\Provision;
use common\models\user\User;
use Decimal\Decimal;
use Yii;
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
class ProvisionForm extends Model {

	public $percent;
	public $hide_on_report;

	/**
	 * @var Provision
	 */
	private Provision $model;

	public function __construct(Provision $model, $config = []) {
		$this->setModel($model);
		parent::__construct($config);
	}

	private function setModel(Provision $model): void {
		$this->model = $model;
		$this->percent = $model->getDivision()->mul(100)->toFixed(2);
		$this->hide_on_report = $model->hide_on_report;
	}

	public function rules(): array {
		return [
			['percent', 'required'],
			['hide_on_report', 'boolean'],
			['percent', 'number', 'min' => 0, 'max' => 100],
		];
	}

	public function attributeLabels(): array {
		return [
			'percent' => Yii::t('provision', 'Provision (%)'),
			'hide_on_report' => Yii::t('provision', 'Hide on report'),
		];
	}

	public function getId(): int {
		return $this->model->id;
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
		if ($this->percent === 0) {
			return $this->model->delete();
		}
		$model = $this->model;
		$percent = new Decimal($this->percent);
		$model->value = Yii::$app->provisions->issuePayValue($this->model->pay)
			->mul($percent)
			->div(100)
			->toFixed(2);
		$model->hide_on_report = $this->hide_on_report;
		return $model->save();
	}
}

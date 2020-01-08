<?php

namespace backend\modules\provision\models;

use common\models\issue\Issue;
use common\models\provision\Provision;
use common\models\User;
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
	private $model;

	public function rules(): array {
		return [
			['hide_on_report', 'boolean'],
			['percent', 'required'],
			['percent', 'number', 'min' => 0, 'max' => 100],
		];
	}

	public function attributeLabels(): array {
		return [
			'percent' => 'Prowizja (%)',
			'hide_on_report' => 'Ukryty w raporcie',
		];
	}

	public function getId(): int {
		return $this->model->id;
	}

	public function setModel(Provision $model): void {
		$this->model = $model;
		$this->percent = $model->provision * 100;
		$this->hide_on_report = $model->hide_on_report;
	}

	public function getToUser(): User {
		return $this->model->toUser;
	}

	public function getIssue(): Issue {
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
		$model->value = $model->pay->value * $this->percent / 100;
		$model->hide_on_report = $this->hide_on_report;
		$model->validate();
		return $model->save();
	}
}

<?php

namespace backend\modules\issue\models;

use common\models\issue\IssueClaim;
use common\models\issue\IssueInterface;
use yii\base\Model;

class IssueClaimsForm extends Model {

	private IssueInterface $issue;

	private ?IssueClaim $customer = null;
	private ?IssueClaim $company = null;

	public function __construct(IssueInterface $issue, $config = []) {
		$this->issue = $issue;
		parent::__construct($config);
	}

	public function getIssue(): IssueInterface {
		return $this->issue;
	}

	public function getCustomer(): IssueClaim {
		if ($this->customer === null) {
			$this->customer = new IssueClaim([
				'type' => IssueClaim::TYPE_CUSTOMER,
				'issue_id' => $this->issue->getIssueId(),
				'scenario' => IssueClaim::SCENARIO_TYPE,
				'date' => time(),
			]);
		}
		return $this->customer;
	}

	public function getCompany(): IssueClaim {
		if ($this->company === null) {
			$this->company = new IssueClaim([
				'type' => IssueClaim::TYPE_COMPANY,
				'issue_id' => $this->issue->getIssueId(),
				'scenario' => IssueClaim::SCENARIO_TYPE,
				'date' => time(),
			]);
		}
		return $this->company;
	}

	public function validate($attributeNames = null, $clearErrors = true) {
		return $this->getCustomer()->validate() && $this->getCompany()->validate();
	}

	public function load($data, $formName = null) {
		return $this->getCustomer()->load($data, $formName)
			&& $this->getCompany()->load($data, $formName);
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		$this->saveModel($this->getCustomer());
		$this->saveModel($this->getCompany());
		return true;
	}

	protected function saveModel(IssueClaim $model): bool {
		if (!empty($model->obtained_value) || !empty($model->trying_value)) {
			return $model->save();
		}
		return false;
	}

}

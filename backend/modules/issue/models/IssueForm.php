<?php

namespace backend\modules\issue\models;

use common\models\address\Address;
use common\models\issue\Issue;
use common\models\entityResponsible\EntityResponsible;
use common\models\issue\IssuePayCity;
use common\models\issue\IssueStage;
use common\models\issue\IssueType;
use common\models\User;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class IssueForm
 *
 * @property $issue Issue
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class IssueForm extends Model {

	public const STAGE_ARCHIVED_ID = IssueStage::ARCHIVES_ID;
	public const ACCIDENT_ID = IssueType::ACCIDENT_ID;
	public const STAGE_POSITIVE_DECISION_ID = IssueStage::POSITIVE_DECISION_ID;

	/** @var Issue */
	private $model;

	/**
	 * @var Address
	 */
	private $payAddress;

	public function setModel(Issue $model): void {
		$this->model = $model;
	}

	public function getModel(): Issue {
		if ($this->model === null) {
			$this->model = new Issue();
		}
		return $this->model;
	}

	public function getPayAddress(): Address {
		if ($this->payAddress === null) {
			if ($this->model->pay_city_id !== null) {
				$this->payAddress = Address::createFromCityId($this->model->pay_city_id);
			} elseif ($this->model->client_city_id !== null) {
				$this->payAddress = Address::createFromCity($this->model->clientCity);
			} else {
				$this->payAddress = new Address();
			}
			$this->payAddress->formName = 'payAddress';
		}
		return $this->payAddress;
	}

	public function load($data, $formName = null): bool {
		$load = $this->getModel()->load($data, $formName);
		if ($load && $this->getModel()->isPositiveDecision()) {
			$load = $load && $this->getPayAddress()->load($data);
		}
		return $load
			&& $this->getModel()->getClientAddress()->load($data)
			&& $this->getModel()->getVictimAddress()->load($data);
	}

	public function validate($attributeNames = null, $clearErrors = true): bool {
		$validate = $this->getModel()->getClientAddress()->validate()
			&& $this->getModel()->getVictimAddress()->validate() && $this->getModel()->validate($attributeNames, $clearErrors);
		if ($validate && $this->getModel()->isPositiveDecision()) {
			$validate = $validate && $this->getPayAddress()->validate($attributeNames, $clearErrors);
		}
		return $validate;
	}

	public function save(): bool {
		if ($this->model->isPositiveDecision()) {
			$this->model->pay_city_id = $this->getPayAddress()->cityId;
		}
		if ($this->validate()) {
			$save = $this->getModel()->save();

			if ($this->getModel()->isPositiveDecision() && !$this->issuePayCityExist($this->model->pay_city_id)) {
				$save = $save && (new IssuePayCity(['city_id' => $this->model->pay_city_id]))->save(false);
			}
			return $save;
		}
		return false;
	}

	public static function getAgents(): array {
		return User::getSelectList([User::ROLE_AGENT, User::ROLE_ISSUE]);
	}

	public static function getLawyers(): array {
		return User::getSelectList([User::ROLE_LAWYER, User::ROLE_ISSUE]);
	}

	public static function getTele(): array {
		return User::getSelectList([User::ROLE_ISSUE, User::ROLE_TELEMARKETER]);
	}

	public static function getTypes(): array {
		return ArrayHelper::map(IssueType::find()->asArray()->all(), 'id', 'name');
	}

	public static function getStages(int $typeID): array {
		$stages = IssueType::findOne($typeID)->stages;
		return ArrayHelper::map($stages, 'id', 'name');
	}

	public static function getEntityResponsibles(): array {
		return ArrayHelper::map(EntityResponsible::find()->asArray()->all(), 'id', 'name');
	}

	private function issuePayCityExist(int $cityId): bool {
		return IssuePayCity::find()->andWhere(['city_id' => $cityId])->exists();
	}

}

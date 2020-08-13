<?php

namespace backend\modules\issue\models;

use common\models\issue\Issue;
use common\models\entityResponsible\EntityResponsible;
use common\models\issue\IssueStage;
use common\models\issue\IssueType;
use common\models\User;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class IssueForm
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class IssueForm extends Model {

	public const STAGE_ARCHIVED_ID = IssueStage::ARCHIVES_ID;
	public const ACCIDENT_ID = IssueType::ACCIDENT_ID;
	public const STAGE_POSITIVE_DECISION_ID = IssueStage::POSITIVE_DECISION_ID;

	/** @var Issue */
	private $model;

	public function setModel(Issue $model): void {
		$this->model = $model;
	}

	public function getModel(): Issue {
		if ($this->model === null) {
			$this->model = new Issue();
		}
		return $this->model;
	}

	public function load($data, $formName = null): bool {
		return $this->getModel()->load($data, $formName)
			&& $this->getModel()->getClientAddress()->load($data)
			&& $this->getModel()->getVictimAddress()->load($data);
	}

	public function validate($attributeNames = null, $clearErrors = true): bool {
		return $this->getModel()->getClientAddress()->validate()
			&& $this->getModel()->getVictimAddress()->validate()
			&& $this->getModel()->validate($attributeNames, $clearErrors);
	}

	public function save(): bool {
		if ($this->validate()) {
			return $this->getModel()->save();
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
		$type = IssueType::get($typeID);
		if ($type === null) {
			return [];
		}
		return ArrayHelper::map($type->stages, 'id', 'name');
	}

	public static function getEntityResponsibles(): array {
		return ArrayHelper::map(EntityResponsible::find()->asArray()->all(), 'id', 'name');
	}

}

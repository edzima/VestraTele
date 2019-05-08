<?php

namespace backend\modules\issue\models;

use common\models\issue\Issue;
use common\models\issue\IssueEntityResponsible;
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

	/** @var Issue */
	private $issue;

	public function rules() {
		return [
			[['Issue'], 'required'],
		];
	}

	public function afterValidate() {
		if (!Model::validateMultiple($this->getAllModels())) {
			$this->addError(null); // add an empty error to prevent saving
		}
		parent::afterValidate();
	}

	public static function getAgents(): array {
		return User::getSelectList([User::ROLE_AGENT]);
	}

	public static function getLawyers(): array {
		return User::getSelectList([User::ROLE_LAYER]);
	}

	public static function getTele(): array {
		return User::getSelectList([User::ROLE_TELEMARKETER]);
	}

	public static function getTypes(): array {
		return ArrayHelper::map(IssueType::find()->asArray()->all(), 'id', 'name');
	}

	public static function getStages(int $typeID): array {
		$stages = IssueType::findOne($typeID)->stages;
		return ArrayHelper::map($stages, 'id', 'name');
	}

	public static function getEntityResponsibles(): array {
		return ArrayHelper::map(IssueEntityResponsible::find()->asArray()->all(), 'id', 'name');
	}

	private function getAllModels() {
		$models = [
			'Issue' => $this->issue,
		];
		foreach ($this->users as $id => $user) {
			$models['Users.' . $id] = $user;
		}
		return $models;
	}
}
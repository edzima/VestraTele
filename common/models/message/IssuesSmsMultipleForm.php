<?php

namespace common\models\message;

use common\models\issue\Issue;
use common\models\issue\IssueInterface;
use common\models\issue\IssueUser;
use common\models\issue\query\IssueUserQuery;
use Generator;
use Yii;
use yii\helpers\ArrayHelper;

class IssuesSmsMultipleForm extends SmsForm {

	public const SCENARIO_DEFAULT = self::SCENARIO_MULTIPLE;
	protected const ISSUE_SMS_CLASS = IssueSmsForm::class;

	public array $ids = [];
	public int $owner_id;
	public array $userTypes = [];

	public function rules(): array {
		return array_merge([
			[['ids', '!owner_id', 'userTypes'], 'required'],
			['!owner_id', 'integer'],
			['userTypes', 'in', 'range' => array_keys(static::getUsersTypesNames()), 'allowArray' => true],
		], parent::rules());
	}

	public function activeAttributes(): array {
		$attributes = parent::activeAttributes();
		ArrayHelper::removeValue($attributes, 'phone');
		ArrayHelper::removeValue($attributes, 'phones');
		return $attributes;
	}

	public function attributeLabels(): array {
		return array_merge([
			'userTypes' => Yii::t('issue', 'User Types'),
		],
			parent::attributeLabels()
		);
	}

	public function send(): bool {
		if (!$this->validate()) {
			return false;
		}
		$models = $this->getIssueSmsModels();
		foreach ($models as $smsModel) {
			$smsModel->send();
		}
		return true;
	}

	public function pushJobs(): array {
		if (!$this->validate()) {
			return [];
		}
		$ids = [];
		foreach ($this->getIssueSmsModels() as $model) {
			$jobsIds = $model->pushJobs();
			if (!empty($jobsIds)) {
				$ids[] = $jobsIds;
			}
		}
		return array_merge([], ...$ids);
	}

	/**
	 * @return Generator|IssueSmsForm[]
	 */
	public function getIssueSmsModels(): Generator {
		$issues = $this->findIssues();
		foreach ($issues as $issue) {
			yield $this->createIssueSmsModel($issue);
		}
	}

	public function createIssueSmsModel(IssueInterface $issue): IssueSmsForm {
		/** @var IssueSmsForm $model */
		$model = Yii::createObject(static::ISSUE_SMS_CLASS, [$issue]);
		$model->userTypes = $this->userTypes;
		$model->owner_id = $this->owner_id;
		$model->setAttributes($this->getAttributes());
		$model->setFirstAvailablePhone();
		return $model;
	}

	/**
	 * @return Generator|IssueInterface[]
	 */
	public function findIssues(): Generator {
		foreach (Issue::find()
			->joinWith([
				'users' => function (IssueUserQuery $query) {
					$query->withTypes($this->userTypes);
					$query->joinWith('user.userProfile');
				},
			])
			->andWhere([Issue::tableName() . '.id' => $this->ids])
			->batch() as $rows) {
			foreach ($rows as $row) {
				yield $row;
			}
		}
	}

	public static function getUsersTypesNames(): array {
		return IssueUser::getTypesNames();
	}

}

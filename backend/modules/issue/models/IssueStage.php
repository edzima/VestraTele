<?php

namespace backend\modules\issue\models;

use common\models\issue\IssueStage as BaseIssueStage;
use common\models\issue\IssueType;

class IssueStage extends BaseIssueStage {

	/**
	 * @var int[]
	 */
	public array $typesIds = [];

	/**
	 * @inheritdoc
	 */
	public function rules(): array {
		return [
			[['name', 'short_name', 'typesIds'], 'required'],
			[['posi', 'days_reminder'], 'integer'],
			['posi', 'default', 'value' => 0],
			['days_reminder', 'integer', 'min' => 1, 'max' => 365],
			[['name', 'short_name'], 'string', 'max' => 255],
			[['name', 'short_name'], 'unique'],
			[
				'typesIds', 'each',
				'rule' => [
					'in', 'range' => array_keys(IssueType::getTypesNames()),
				],
			],
		];
	}

	public function afterFind() {
		parent::afterFind();
		$this->typesIds = array_map('intval', $this->getTypes()->select('id')->column());
	}

	public function afterSave($insert, $changedAttributes) {
		if (!$insert) {
			$this->unlinkAll('types', true);
		}
		foreach ($this->typesIds as $typeId) {
			$this->link('types', IssueType::get($typeId));
		}

		return parent::afterSave($insert, $changedAttributes);
	}

}

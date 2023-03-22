<?php

namespace backend\modules\issue\models;

use common\helpers\ArrayHelper;
use common\models\issue\IssueTag;
use common\models\issue\IssueTagType;
use yii\base\Model;

class TypeTagsForm extends Model {

	private IssueTagType $type;

	public $tags = [];

	public function attributeLabels(): array {
		return [
			'tags' => \Yii::t('common', 'Tags'),
		];
	}

	public function rules(): array {
		return [
			['tags', 'required'],
			['tags', 'in', 'range' => array_keys(static::getTagsNames(false)), 'allowArray' => true],
		];
	}

	public function setType(IssueTagType $type): void {
		$this->type = $type;
		$this->tags = ArrayHelper::getColumn($type->issueTags, 'id');
	}

	public static function getTagsNames(bool $active): array {
		return IssueTag::getNames($active);
	}

	public function getType(): IssueTagType {
		return $this->type;
	}

	public function save(): bool {
		if ($this->validate()) {
			IssueTag::updateAll(['type' => null], [
				'type' => $this->type->id,
			]);
			IssueTag::updateAll(['type' => $this->type->id], [
				'id' => $this->tags,
			]);
			return true;
		}
		return false;
	}
}

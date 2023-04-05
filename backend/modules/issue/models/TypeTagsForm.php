<?php

namespace backend\modules\issue\models;

use common\helpers\ArrayHelper;
use common\models\issue\Issue;
use common\models\issue\IssueTag;
use common\models\issue\IssueTagType;
use Yii;
use yii\base\Model;

class TypeTagsForm extends Model {

	private IssueTagType $type;

	public $tags = [];

	public function attributeLabels(): array {
		return [
			'tags' => Yii::t('common', 'Tags'),
		];
	}

	public function rules(): array {
		return [
			['tags', 'newTagsFilter'],
		];
	}

	public function newTagsFilter(): void {
		$tags = static::getTagsNames(false);
		foreach ((array) $this->tags as $key => $tagIdOrNewName) {
			if (!isset($tags[$tagIdOrNewName])) {
				unset($this->tags[$key]);
				if (!empty($tagIdOrNewName) && !is_numeric($tagIdOrNewName)) {
					$tag = new IssueTag();
					$tag->is_active = true;
					$tag->name = $tagIdOrNewName;
					$tag->type = $this->type->id;
					$tag->save();
					if ($tag->id) {
						$this->tags[] = $tag->id;
					}
				}
			}
		}
	}

	protected function addTag(string $name, ?int $type): IssueTag {
		$tag = new IssueTag();
		$tag->is_active = true;
		$tag->name = $name;
		$tag->type = $type;
		return $tag;
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

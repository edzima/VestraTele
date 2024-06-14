<?php

namespace backend\modules\issue\models;

use common\models\issue\IssueTag;
use common\models\issue\IssueTagLink;
use common\models\issue\IssueTagType;
use Yii;
use yii\base\Model;

class IssueTagsLinkForm extends Model {

	public const SCENARIO_MULTIPLE_ISSUES = 'multiple_issues';

	public array $issuesIds = [];
	public $withoutType = [];
	public $typeTags = [];
	private ?array $_tags = null;

	public function rules(): array {
		return [
			['withoutType', 'withoutTypeTagsFilter'],
			['typeTags', 'typeTagsFilter'],
			['issuesIds', 'required', 'on' => static::SCENARIO_MULTIPLE_ISSUES],
		];
	}

	public function attributeLabels(): array {
		return [
			'withoutType' => Yii::t('common', 'Tags without Type'),
		];
	}

	public function typeTagsFilter(): void {
		foreach ($this->typeTags as $typeId => $ids) {
			if (!empty($ids)) {
				$typesTags = $this->getTagsNames($typeId);
				foreach ((array) $ids as $key => $tagIdOrNewName) {
					if (!isset($typesTags[$tagIdOrNewName])) {
						unset($this->typeTags[$typeId][$key]);
						if (!empty($tagIdOrNewName) && !is_numeric($tagIdOrNewName)) {
							$tag = $this->addTag($tagIdOrNewName, $typeId);
							if ($tag->id) {
								$this->typeTags[$typeId][] = $tag->id;
							}
						}
					}
				}
			}
		}
	}

	public function withoutTypeTagsFilter(): void {
		$tags = $this->getTagsNames(null);
		foreach ((array) $this->withoutType as $key => $tagIdOrNewName) {
			if (!isset($tags[$tagIdOrNewName])) {
				unset($this->withoutType[$key]);
				if (!empty($tagIdOrNewName) && !is_numeric($tagIdOrNewName)) {
					$tag = $this->addTag($tagIdOrNewName, null);
					if ($tag->id) {
						$this->withoutType[] = $tag->id;
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
		if ($tag->save()) {
			$this->_tags[] = $tag;
		}
		return $tag;
	}

	public function getTypes(): array {
		return IssueTagType::getTypesNames();
	}

	public function getTagsNames(?int $type): array {
		$names = [];
		foreach ($this->getTags() as $tag) {
			if ($tag->type === $type) {
				$names[$tag->id] = $tag->name;
			}
		}
		return $names;
	}

	/**
	 * @return IssueTag[]
	 */
	protected function getTags(): array {
		if (empty($this->_tags)) {
			$this->_tags = IssueTag::getModels();
		}
		return $this->_tags;
	}

	public function setIssueTags(int $issueId): void {
		$this->setTagsIds(
			IssueTagLink::find()
				->select('tag_id')
				->andWhere(['issue_id' => $issueId])
				->column()
		);
	}

	public function linkIssue(int $issueId): bool {
		if (!$this->validate()) {
			return false;
		}
		IssueTagLink::deleteAll([
			'issue_id' => $issueId,
		]);
		$rows = [];
		$ids = $this->getTagsIds();
		foreach ($ids as $id) {
			$rows[] = [
				'issue_id' => $issueId,
				'tag_id' => $id,
			];
		}

		if (!empty($rows)) {
			IssueTagLink::getDb()->createCommand()
				->batchInsert(IssueTagLink::tableName(), ['issue_id', 'tag_id'], $rows)
				->execute();
		}
		return true;
	}

	public function linkMultiple(): bool {
		if (!$this->validate()) {
			return false;
		}
		$tagsIds = $this->getTagsIds();
		if (empty($tagsIds)) {
			return false;
		}

		IssueTagLink::deleteAll([
			'tag_id' => $tagsIds,
			'issue_id' => $this->issuesIds,
		]);

		$rows = [];

		foreach ($tagsIds as $tagId) {
			foreach ($this->issuesIds as $issueId) {
				$rows[] = [
					'issue_id' => $issueId,
					'tag_id' => $tagId,
				];
			}
		}

		if (!empty($rows)) {
			IssueTagLink::getDb()->createCommand()
				->batchInsert(IssueTagLink::tableName(), ['issue_id', 'tag_id'], $rows)
				->execute();
		}
		return true;
	}

	public function getTagsIds(): array {
		$ids = (array) $this->withoutType;
		foreach ($this->typeTags as $typesIds) {
			if (!empty($typesIds)) {
				$ids = array_merge($ids, $typesIds);
			}
		}
		return array_filter($ids, function ($value) {
			return !empty($value);
		});
	}

	public function setTagsIds(array $tagsIds): void {
		$withoutTypes = $this->getTagsNames(null);
		foreach ($tagsIds as $id) {
			foreach ($this->getTypes() as $typeId => $name) {
				$typesTags = $this->getTagsNames($typeId);
				if (isset($typesTags[$id])) {
					$this->typeTags[$typeId][] = $id;
					break;
				}
			}
			if (isset($withoutTypes[$id])) {
				$this->withoutType[] = $id;
			}
		}
	}

}

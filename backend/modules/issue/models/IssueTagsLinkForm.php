<?php

namespace backend\modules\issue\models;

use common\models\issue\Issue;
use common\models\issue\IssueInterface;
use common\models\issue\IssueTag;
use common\models\issue\IssueTagLink;
use common\models\issue\IssueTagType;
use Yii;
use yii\base\Model;

class IssueTagsLinkForm extends Model {

	private IssueInterface $issue;
	public $withoutType = [];
	public $typeTags = [];
	private ?array $_tags = null;

	public function rules(): array {
		return [
			['withoutType', 'in', 'range' => array_keys($this->getTagsNames(null)), 'allowArray' => true],
			['typeTags', 'typeTagsFilter'],
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
						//		var_dump($this->typeTags[$typeId]);
						unset($this->typeTags[$typeId][$key]);
						//var_dump($this->typeTags);
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

	protected function addTag(string $name, ?int $type): IssueTag {
		$tag = new IssueTag();
		$tag->is_active = true;
		$tag->name = $name;
		$tag->type = $type;
		if ($tag->save()) {
			var_dump('save tag');
			$this->_tags[] = $tag;
		}else{
			var_dump($tag->getErrors());
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

	public function setIssue(IssueInterface $issue): void {
		$this->issue = $issue;
		$this->setTagsIds(
			IssueTagLink::find()
				->select('tag_id')
				->andWhere(['issue_id' => $issue->getIssueId()])
				->column()
		);
	}

	public function getIssue(): IssueInterface {
		return $this->issue;
	}

	public function save(): bool {
		if (!$this->validate()) {
			return false;
		}
		IssueTagLink::deleteAll(['issue_id' => $this->issue->getIssueId()]);
		$rows = [];
		$ids = $this->getTagsIds();
		foreach ($ids as $id) {
			$rows[] = [
				'issue_id' => $this->getIssue()->getIssueId(),
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

	private function getTagsIds(): array {
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

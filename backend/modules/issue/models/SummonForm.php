<?php

namespace backend\modules\issue\models;

use common\models\issue\form\SummonForm as BaseSummonForm;
use common\models\issue\Summon;
use common\models\issue\SummonDocLink;
use yii\base\InvalidConfigException;

/**
 * Form model for summon in backend app.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class SummonForm extends BaseSummonForm {

	public function createMultiple(bool $validate = true): ?int {
		if ($validate && !$this->validate()) {
			return null;
		}
		if (!$this->save()) {
			return null;
		}
		$model = $this->getModel();
		$count = 1;
		$rows = [];
		$attributes = $model->getAttributes();
		unset($attributes['id']);
		$keys = array_keys($attributes);
		foreach ($this->issuesIds as $issueId) {
			if ($issueId != $model->issue_id) {
				$attributes['issue_id'] = $issueId;
				$rows[$issueId] = $attributes;
			}
		}
		if (!empty($rows)) {
			$count += Summon::getDb()
				->createCommand()
				->batchInsert(
					Summon::tableName(),
					$keys,
					$rows)
				->execute();

			$this->multipleLinkDocLink();
		}

		return $count;
	}

	private function multipleLinkDocLink(): ?int {
		$docTypesIds = $this->doc_types_ids;
		if (empty($docTypesIds)) {
			return null;
		}
		$docTypesIds = (array) $docTypesIds;
		$rows = [];
		$summonsIds = $this->getCreatedSummonsIds();
		foreach ($summonsIds as $summonId) {
			foreach ($docTypesIds as $doc_type_id) {
				$rows[] = [
					'doc_type_id' => $doc_type_id,
					'summon_id' => $summonId,
				];
			}
		}
		if (empty($rows)) {
			return null;
		}
		return SummonDocLink::getDb()
			->createCommand()
			->batchInsert(SummonDocLink::tableName(),
				[
					'doc_type_id',
					'summon_id',
				],
				$rows)
			->execute();
	}

	private function getCreatedSummonsIds(): array {
		$model = $this->getModel();
		if (empty($model->id)) {
			throw new InvalidConfigException('model must be save.');
		}
		if (empty($model->created_at)) {
			$model->refresh();
		}
		return Summon::find()
			->select('id')
			->andWhere([
				'issue_id' => $this->issuesIds,
				'owner_id' => $this->owner_id,
			])
			->andWhere(['NOT IN', 'id', $this->getModel()->id]) //base summon model already link docs
			->andWhere(['>=', Summon::tableName() . '.created_at', $model->created_at])
			->column();
	}
}

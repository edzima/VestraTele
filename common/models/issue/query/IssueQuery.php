<?php

namespace common\models\issue\query;

use common\models\issue\Issue;
use common\models\issue\IssueStage;
use common\models\issue\IssueType;
use common\models\issue\IssueUser;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Issue]].
 *
 * @see Issue
 */
class IssueQuery extends ActiveQuery {

	public function type(int $typeId, bool $withChildren = true): self {
		$types = [];
		if ($withChildren) {
			$type = IssueType::getTypes()[$typeId] ?? null;
			if ($type) {
				$types = $type->getAllChildesIds();
			}
		}
		$types[] = $typeId;
		$this->andWhere([Issue::tableName() . '.type_id' => $types]);
		return $this;
	}

	public function types(array $types, bool $withChildren = true): self {
		$ids = $types;
		if ($withChildren) {
			foreach ($types as $typeId) {
				$type = IssueType::getTypes()[$typeId] ?? null;
				if ($type !== null) {
					$children = $type->getAllChildesIds();
					if (!empty($children)) {
						$ids = array_merge($ids, $children);
					}
				}
			}
			$ids = array_unique($ids);
		}

		$this->andWhere([Issue::tableName() . '.type_id' => $ids]);
		return $this;
	}

	public function withoutCustomer(): self {
		$this->andWhere([
			'NOT IN', 'id', IssueUser::find()
				->withType(IssueUser::TYPE_CUSTOMER)
				->select('issue_id')
				->column(),
		]);
		return $this;
	}

	public function onlyPayed(): self {
		$this->andWhere(['payed' => true]);
		return $this;
	}

	public function onlyWithoutPay(): self {
		$this->joinWith('pays');
		$this->andWhere('issue_pay.id IS NULL');
		return $this;
	}

	public function onlyPartPay(): self {
		$this->joinWith('pays');
		$this->andWhere('issue_pay.id IS NOT NULL');
		return $this;
	}

	public function withoutArchives(): self {
		$this->andWhere(['not', ['issue.stage_id' => IssueStage::ARCHIVES_IDS]]);
		return $this;
	}

	public function withoutArchiveDeep(): self {
		$this->andWhere(['not', ['issue.stage_id' => IssueStage::ARCHIVES_DEEP_ID]]);
		return $this;
	}

	public function agents(array $ids, string $alias = null): self {
		return $this->users(IssueUser::TYPE_AGENT, $ids, $alias);
	}

	public function lawyers(array $ids, string $alias = null): self {
		return $this->users(IssueUser::TYPE_LAWYER, $ids, $alias);
	}

	public function tele(array $ids, string $alias = null): self {
		return $this->users(IssueUser::TYPE_TELEMARKETER, $ids, $alias);
	}

	public function users(string $type, array $ids, string $alias = null): self {
		if (!empty($ids)) {
			if ($alias === null) {
				$alias = $type;
			}
			$this->joinWith('users ' . $alias);
			$this->andWhere([
				$alias . '.type' => $type,
				$alias . '.user_id' => $ids,
			]);
		}
		return $this;
	}

	/**
	 * @inheritdoc
	 * @return Issue[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return Issue|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}

}

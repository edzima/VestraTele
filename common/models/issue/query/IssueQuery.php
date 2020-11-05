<?php

namespace common\models\issue\query;

use common\models\issue\Issue;
use common\models\issue\IssueStage;
use common\models\issue\IssueUser;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Issue]].
 *
 * @see Issue
 */
class IssueQuery extends ActiveQuery {

	public function withoutCustomer(): self {
		$this->andWhere([
			'NOT IN', 'id', IssueUser::find()
				->withType(IssueUser::TYPE_CUSTOMER)
				->select('issue_id')
				->column(),
		]);
		return $this;
	}

	public function onlyPositiveDecision(): self {
		$this->andWhere(['stage_id' => IssueStage::POSITIVE_DECISION_ID]);
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

	public function onlyForLawyer(int $id): self {
		$this->andWhere(['lawyer_id' => $id]);
		return $this;
	}

	public function onlyForTele(int $id): self {
		$this->andWhere(['tele_id' => $id]);
		return $this;
	}

	public function onlyForAgents(array $ids): self {
		$this->andWhere(['agent_id' => $ids]);
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

	public function withoutArchives(): self {
		$this->andWhere(['not', ['stage_id' => IssueStage::ARCHIVES_ID]]);
		return $this;
	}
}

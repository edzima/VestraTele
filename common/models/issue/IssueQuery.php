<?php

namespace common\models\issue;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Issue]].
 *
 * @see Issue
 */
class IssueQuery extends ActiveQuery {

	public function onlyPositiveDecision(): self {
		$this->andWhere(['stage_id' => IssueStage::POSITIVE_DECISION_ID]);
		return $this;
	}

	public function onlyPayed() {
		$this->andWhere(['payed' => true]);
		return $this;
	}

	public function onlyWithoutPay() {
		$this->joinWith('pays');
		$this->andWhere('issue_pay.id IS NULL');
		return $this;
	}

	public function onlyPartPay() {
		$this->joinWith('pays');
		$this->andWhere('issue_pay.id IS NOT NULL');
		return $this;
	}

	public function onlyForLawyer(int $id) {
		$this->andWhere(['lawyer_id' => $id]);
		return $this;
	}

	public function onlyForTele(int $id) {
		$this->andWhere(['tele_id' => $id]);
		return $this;
	}

	public function onlyForAgents(array $ids) {
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

	public function withoutArchives() {
		$this->andWhere(['not in', 'stage_id', IssueStage::ARCHIVES_ID]);
		return $this;
	}
}

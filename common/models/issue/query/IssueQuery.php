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

	public function withoutArchives(): self {
		$this->andWhere(['not', ['stage_id' => IssueStage::ARCHIVES_ID]]);
		return $this;
	}

	public function agents(array $ids): self {
		return $this->users(IssueUser::TYPE_AGENT, $ids);
	}

	public function lawyers(array $ids): self {
		return $this->users(IssueUser::TYPE_LAWYER, $ids);
	}

	public function tele(array $ids): self {
		return $this->users(IssueUser::TYPE_TELEMARKETER, $ids);
	}

	protected function users(string $type, array $ids): self {
		if (!empty($ids)) {
			$this->joinWith('users');
			$this->andWhere([
				'issue_user.type' => $type,
				'issue_user.user_id' => $ids,
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

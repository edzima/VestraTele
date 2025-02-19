<?php

namespace common\modules\lead\models\query;

use common\helpers\ArrayHelper;
use common\models\query\IdsActiveQuery;
use common\models\query\PhonableQuery;
use common\models\query\PhonableQueryTrait;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadUser;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for Lead.
 *
 * @see Lead
 */
class LeadQuery extends ActiveQuery implements PhonableQuery, IdsActiveQuery {

	use PhonableQueryTrait;

	/**
	 * @inheritdoc
	 * @return Lead[]|array
	 */
	public function all($db = null) {
		return parent::all($db);
	}

	/**
	 * @inheritdoc
	 * @return Lead|array|null
	 */
	public function one($db = null) {
		return parent::one($db);
	}

	public function withoutUsers(): self {
		$this->joinWith([
			'leadUsers' => function (ActiveQuery $query) {
				$query->andWhere(LeadUser::tableName() . '.lead_id IS NULL');
			},
		], false);
		return $this;
	}

	public function dateBetween(?string $fromAt, ?string $toAt, bool $day = true): self {
		if ($day) {
			$fromAt = $fromAt ? date('Y-m-d 00:00:00', strtotime($fromAt)) : null;
			$toAt = $toAt ? date('Y-m-d 23:59:59', strtotime($toAt)) : null;
		}
		if ($fromAt) {
			$this->andWhere([
				'>=', Lead::tableName() . '.date_at', $fromAt,
			]);
		}
		if ($toAt) {
			$this->andWhere([
				'<=', Lead::tableName() . '.date_at', $toAt,
			]);
		}
		return $this;
	}

	public function owner(int $user_id): self {
		$this->joinWith([
			'leadUsers' => function (ActiveQuery $query) use ($user_id) {
				$query->andWhere([
					LeadUser::tableName() . '.user_id' => $user_id,
					LeadUser::tableName() . '.type' => LeadUser::TYPE_OWNER,
				]);
			},
		]);
		return $this;
	}

	public function user(int $user_id, string $type = null): self {
		$this->joinWith([
			'leadUsers' => function (ActiveQuery $query) use ($user_id, $type) {
				$query->andWhere([
					LeadUser::tableName() . '.user_id' => $user_id,
				]);
				if ($type) {
					$query->andWhere([
						LeadUser::tableName() . '.type' => $type,
					]);
				}
			},
		]);
		return $this;
	}

	public function dialer(int $user_id): self {
		$this->user($user_id, LeadUser::TYPE_DIALER);
		return $this;
	}

	public function type(int $type_id): self {
		$this->joinWith('leadSource S');
		$this->andWhere(['S.type_id' => $type_id]);
		return $this;
	}

	public function onlyNewestReport(): self {
		$this->joinWith(['reports']);
		$this->andWhere([
			'=',
			LeadReport::tableName() . '.id',
			LeadReport::find()
				->select('MAX(' . LeadReport::tableName() . '.id)')
				->andWhere(LeadReport::tableName() . '.lead_id = ' . Lead::tableName() . '.id'),
		]);
		return $this;
	}

	public function statusesCounts(array $statuses = []): array {
		$this->andFilterWhere([Lead::tableName() . '.status_id' => $statuses]);
		$this->select([Lead::tableName() . '.status_id', 'count(*) as count'])
			->groupBy(Lead::tableName() . '.status_id');
		$data = $this->asArray()->all();
		$data = ArrayHelper::map($data, 'status_id', 'count');
		$data = array_map('intval', $data);
		return $data;
	}

	public function getIds(): array {
		$self = clone $this;
		$self->select(Lead::tableName() . '.id');
		$self->groupBy(Lead::tableName() . '.id');
		return $self->column();
	}
}

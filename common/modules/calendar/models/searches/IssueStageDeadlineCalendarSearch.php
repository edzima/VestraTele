<?php

namespace common\modules\calendar\models\searches;

use common\helpers\Html;
use common\models\issue\Issue;
use common\models\issue\IssueStage;
use common\models\issue\IssueUser;
use common\models\issue\query\IssueQuery;
use common\models\user\User;
use common\modules\calendar\models\IssueStageDeadlineEvent;
use yii\base\Model;

class IssueStageDeadlineCalendarSearch extends Model {

	public string $start;
	public string $end;

	public static function getLawyersFilters(): array {
		$stages = static::getStages();
		if (empty($stages)) {
			return [];
		}

		$usersIds = IssueUser::find()
			//->select('user_id')
			->joinWith('issue')
			->andWhere([Issue::tableName() . '.stage_id' => array_keys($stages)])
			->withType(IssueUser::TYPE_LAWYER)
			->andWhere(['user_id' => 496])
			->distinct()
			->all();
		//->column();
		var_dump($usersIds);
		if (empty($usersIds)) {
			return [];
		}
		return User::getSelectList($usersIds, false);
	}

	/**
	 * @return IssueStage[]
	 */
	public static function getStages(): array {
		return IssueStageDeadlineEvent::getStages();
	}

	public static function getStagesFilters(): array {
		$data = [];
		foreach (static::getStages() as $model) {
			$color = $model->calendar_background;
			$data[] = [
				'value' => $model->id,
				'label' => Html::encode($model->name),
				'isActive' => true,
				'color' => $color,
				'badge' => [
					'background' => $color,
					'text' => Html::encode($model->short_name),
				],
			];
		}
		return $data;
	}

	public function getEventsData(string $urlRoute): array {
		$query = $this->getQuery();
		if ($query === null) {
			return [];
		}
		$query->with(
			'customer',
			'newestNote',
			'lawyer'
		);
		$data = [];
		foreach ($query->all() as $model) {
			$event = new IssueStageDeadlineEvent();
			$event->setUrlRoute($urlRoute);
			$event->setModel($model);
			$data[] = $event->toArray();
		}

		return $data;
	}

	public function getQuery(): ?IssueQuery {
		$stages = static::getStages();
		if (empty($stages)) {
			return null;
		}
		return Issue::find()
			->andWhere(['stage_id' => array_keys($stages)])
			->andWhere([
				'>=', 'stage_deadline_at', $this->start,
			])
			->andWhere([
				'<=', 'stage_deadline_at', $this->end,
			]);
	}
}

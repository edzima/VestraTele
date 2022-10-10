<?php

namespace common\modules\calendar\models\searches;

use common\helpers\ArrayHelper;
use common\helpers\Html;
use common\models\issue\Issue;
use common\models\issue\IssueStage;
use common\models\issue\IssueUser;
use common\models\issue\query\IssueQuery;
use common\models\KeyStorageItem;
use common\models\user\User;
use common\modules\calendar\models\IssueStageDeadlineEvent;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;
use yii\helpers\Json;

class IssueStageDeadlineCalendarSearch extends Model {

	public string $start;
	public string $end;

	public static function getLawyersFilters(): array {
		$stages = static::getStages();
		if (empty($stages)) {
			return [];
		}

		$usersIds = IssueUser::find()
			->select('user_id')
			->joinWith('issue')
			->andWhere([Issue::tableName() . '.stage_id' => array_keys($stages)])
			->withType(IssueUser::TYPE_LAWYER)
			->distinct()
			->column();
		if (empty($usersIds)) {
			return [];
		}
		$users = User::getSelectList($usersIds);
		$data = [];
		$colorsData = static::getLawyersBackgrounds($users);
		foreach ($users as $id => $name) {
			$names = explode(' ', $name);
			$firstNames = [];
			foreach ($names as $partName) {
				$firstNames[] = mb_strimwidth($partName, 0, 1);
			}
			$color = $colorsData[$id];
			$data[] = [
				'value' => $id,
				'label' => Html::encode($name),
				'isActive' => true,
				'color' => $color,
				'badge' => [
					'text' => implode(' ', $firstNames),
					'background' => $color,
				],
			];
		}
		return $data;
	}

	protected static function getLawyersBackgrounds(array $names): array {
		try {
			$data = Json::decode(Yii::$app->keyStorage->get(KeyStorageItem::KEY_CALENDAR_USERS_BACKGROUND));
		} catch (InvalidArgumentException $exception) {
			$data = [];
		}
		$colorsData = [];
		if (isset($data['lawyers'])) {
			$colorsData = ArrayHelper::map($data['lawyers'], 'id', 'background');
		}
		foreach ($names as $id => $name) {
			if (!isset($colorsData[$id])) {
				$colorsData[$id] = '#' . substr(bin2hex($name), -6);
			}
		}
		return $colorsData;
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

	/**
	 * @return IssueStage[]
	 */
	public static function getStages(): array {
		return IssueStageDeadlineEvent::getStages();
	}
}

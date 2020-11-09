<?php

namespace backend\modules\issue\models\search;

use common\models\issue\IssueSearch as BaseIssueSearch;
use common\models\issue\query\IssueQuery;
use common\models\user\Worker;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * IssueSearch model for backend app.
 *
 * @author Łukasz Wojda <lukasz.wojda@protonmail.com>
 */
class IssueSearch extends BaseIssueSearch {

	public $parentId;
	public $excludedStages = [];
	public bool $onlyDelayed = false;

	public function rules(): array {
		return array_merge(parent::rules(), [
			['parentId', 'integer'],
			['onlyDelayed', 'boolean'],
			['excludedStages', 'in', 'range' => array_keys($this->getStagesNames()), 'allowArray' => true],
		]);
	}

	public function attributeLabels(): array {
		return array_merge(parent::attributeLabels(), [
			'parentId' => Yii::t('backend', 'Structures'),
			'excludedStages' => Yii::t('backend', 'Excluded stages'),
			'onlyDelayed' => Yii::t('backend', 'Only delayed'),
		]);
	}

	public function search(array $params): ActiveDataProvider {
		$provider = parent::search($params);
		/** @var IssueQuery $query */
		$query = $provider->query;
		$query->with('entityResponsible');
		$this->delayedFilter($query);
		$this->excludedStagesFilter($query);
		return $provider;
	}

	protected function agentFilter(IssueQuery $query): void {
		$ids = [];
		if (!empty($this->agent_id)) {
			$ids[] = $this->agent_id;
		}
		if ($this->parentId > 0) {
			$user = Worker::findOne($this->parentId);
			if ($user !== null) {
				$ids = $user->getAllChildesIds();
				$ids[] = $user->id;
			}
		}
		$query->agents($ids);
	}

	private function delayedFilter(IssueQuery $query): void {
		if (!empty($this->onlyDelayed)) {
			$query->joinWith('stage');
			$daysGroups = ArrayHelper::map($this->getStagesNames(), 'id', 'days_reminder', 'days_reminder');

			foreach ($daysGroups as $day => $ids) {
				if (!empty($day)) {
					$query->orFilterWhere([
						'and',
						[
							'stage_id' => array_keys($ids),
						],
						[
							'<=', new Expression("DATE_ADD(stage_change_at, INTERVAL $day DAY)"), new Expression('NOW()'),
						],
					]);
				}
			}
			$query->andWhere('stage_change_at IS NOT NULL');
			$query->andWhere('issue_stage.days_reminder is NOT NULL');
		}
	}

	protected function excludedStagesFilter(IssueQuery $query): void {
		$query->andFilterWhere(['NOT IN', 'stage_id', $this->excludedStages]);
	}

}

<?php

namespace backend\modules\issue\models\search;

use backend\modules\issue\models\IssueArchive;
use backend\modules\issue\models\IssueStage;
use common\models\issue\Issue;
use common\models\issue\IssueType;
use common\models\SearchModel;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;

class IssueArchiveSearch extends Model implements SearchModel {

	public $max_stage_change_at;
	public $issue_id;
	public $archives_nr;

	public $type_id;
	public $stage_id;

	public $count;

	/**
	 * {@inheritdoc}
	 */
	public function rules(): array {
		return [
			[['stage_id', 'type_id', 'issue_id'], 'integer', 'allowArray' => true],
			[['archives_nr'], 'string'],
			['max_stage_change_at', 'safe'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function attributeLabels(): array {
		return [
			'count' => Yii::t('issue', 'Issues Count'),
			'type_id' => Yii::t('issue', 'Type'),
			'stage_id' => Yii::t('issue', 'Stage'),
			'issue_id' => Yii::t('issue', 'Issue'),
			'max_stage_change_at' => Yii::t('issue', 'Stage Change At'),
		];
	}

	public static function getTypesNames(): array {
		$rows = IssueArchive::find()
			->select(['type_id', 'count(*) as count'])
			->groupBy('archives_nr')
			->distinct()
			->asArray()
			->all();

		$names = [];
		foreach ($rows as $row) {
			$id = $row['type_id'];
			$names[$id] = IssueType::getTypesNames()[$id] . ' (' . $row['count'] . ')';
		}
		return $names;
	}

	public static function getStagesNames(): array {
		$rows = IssueArchive::find()
			->select(['stage_id', 'count(*) as count'])
			->groupBy('archives_nr')
			->distinct()
			->asArray()
			->all();

		$names = [];
		foreach ($rows as $row) {
			$id = $row['stage_id'];
			$names[$id] = IssueStage::getStagesNames(true, true)[$id] . ' (' . $row['count'] . ')';
		}
		return $names;
	}

	public function search(array $params): DataProviderInterface {
		$query = IssueArchive::find();
		$query->select([
			'archives_nr',
			'count(*) as count',
			'max(stage_change_at) as max_stage_change_at',
		]);
		$query->groupBy('archives_nr');

		$dataProvider = new ActiveDataProvider([
			'key' => 'archives_nr',
			'query' => $query,
			'sort' => [
				'attributes' => [
					'archives_nr',
					'count',
					'max_stage_change_at',
				],
				'defaultOrder' => ['max_stage_change_at' => SORT_DESC],
			],
		]);

		$this->load($params);

		if (!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		$query->andFilterWhere([Issue::tableName() . '.id' => $this->issue_id])
			->andFilterWhere([Issue::tableName() . '.stage_id' => $this->stage_id])
			->andFilterWhere([Issue::tableName() . '.type_id' => $this->type_id])
			->andFilterWhere([Issue::tableName() . '.stage_change_at' => $this->max_stage_change_at])
			->andFilterWhere(['like', Issue::tableName() . '.archives_nr', $this->archives_nr]);

		return $dataProvider;
	}
}

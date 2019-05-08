<?php
/**
 * Created by PhpStorm.
 * User: edzima
 * Date: 2019-04-08
 * Time: 16:38
 */

namespace console\components\oldCrmData;

use common\models\issue\IssueNote;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

class IssueNoteDataTransfer extends DataTransfer {

	public $issueId;

	/* @var ActiveRecord */
	public $model = IssueNote::class;
	public $oldTableName = '{{%notes}}';
	public $oldColumns = [
		'issue_id',
		'user_id',
		'datestamp',
		'name',
		'note',
	];

	public function init() {
		$issueId = $this->issueId;
		if (empty($this->queryCondition)) {
			$this->queryCondition = function (Query $query) use ($issueId) {
				$query->andWhere(['issue_id' => $issueId]);
			};
		}

		parent::init();
	}

	public function transfer(): void {
		foreach ($this->getBatchResult() as $rows) {
			$data = [];
			foreach ($rows as $row) {
				$data[] = [
					'issue_id' => $this->issueId,
					'user_id' => Yii::$app->userData->getUserId($row['user_id']),
					'created_at' => $row['datestamp'],
					'updated_at' => $row['datestamp'],
					'title' => $row['name'],
					'description' => $row['note'],
				];
			}
			$this->batchInsert([
				'issue_id',
				'user_id',
				'created_at',
				'updated_at',
				'title',
				'description',
			], $data);
		}
	}

	private function batchInsert(array $columns, array $rows): void {
		Yii::$app->db->createCommand()
			->batchInsert(
				$this->model::tableName(),
				$columns,
				$rows)
			->execute();
	}
}
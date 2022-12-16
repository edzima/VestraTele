<?php

use console\base\Migration;
use yii\db\Query;

/**
 * Class m221213_124116_issue_types_structure
 */
class m221213_124116_issue_types_structure extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue_type}}', 'parent_id', $this->integer());

		$this->dropColumn('{{%issue_type}}', 'meet');
		$this->dropColumn('{{%issue_type}}', 'provision_type');

		$this->addColumn('{{%issue_stage_type}}', 'days_reminder', $this->smallInteger()->null());
		$this->addColumn('{{%issue_stage_type}}', 'calendar_background', $this->string()->null());

		$stages = (new Query())
			->select(['id', 'days_reminder'])
			->from('{{%issue_stage}}')
			->andWhere(['>', 'days_reminder', 0])
			->orWhere('calendar_background IS NOT NULL')
			->all();

		$daysStages = [];
		$calendarStages = [];
		foreach ($stages as $data) {
			$id = $data['id'];
			$days = $data['days_reminder'];
			$calendar = $data['calendar_background'];
			if (!empty($days)) {
				$daysStages[$days][] = $id;
			}
			if (!empty($calendar)) {
				$calendarStages[$calendar][] = $id;
			}
		}
		foreach ($daysStages as $days => $ids) {
			$this->update('{{%issue_stage_type}}', ['days_reminder' => $days], [
				'stage_id' => $ids,
			]);
		}

		foreach ($calendarStages as $calendar => $ids) {
			$this->update('{{%issue_stage_type}}', ['calendar_background' => $calendar], [
				'stage_id' => $ids,
			]);
		}

		//$this->dropColumn('{{%issue_stage}}', 'days_reminder');
		//$this->dropColumn('{{%issue_stage}}', 'calendar_background');

		$this->addPrimaryKey('{{%PK_issue_stage_type}}', '{{%issue_stage_type}}', ['stage_id', 'type_id']);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropPrimaryKey('{{%PK_issue_stage_type}}', '{{%issue_stage_type}}');

		//	$this->addColumn('{{%issue_stage}}', 'days_reminder', $this->smallInteger()->null());
		//	$this->addColumn('{{%issue_stage}}', 'calendar_background', $this->string()->null());

		$stages = (new Query())
			->select(['stage_id', 'days_reminder'])
			->from('{{%issue_stage_type}}')
			->andWhere(['>', 'days_reminder', 0])
			->orWhere('calendar_background IS NOT NULL')
			->all();

		$daysStages = [];
		$calendarStages = [];

		foreach ($stages as $data) {
			$id = $data['stage_id'];
			$days = $data['days_reminder'];
			$calendar = $data['calendar_background'];
			if (!empty($days)) {
				$daysStages[$days][] = $id;
			}
			if (!empty($calendar)) {
				$calendarStages[$calendar][] = $id;
			}
		}
		foreach ($daysStages as $days => $ids) {
			$this->update('{{%issue_stage}}', ['days_reminder' => $days], [
				'id' => $ids,
			]);
		}

		foreach ($calendarStages as $calendar => $ids) {
			$this->update('{{%issue_stage}}', ['calendar_background' => $calendar], [
				'id' => $ids,
			]);
		}

		$this->dropColumn('{{%issue_stage_type}}', 'days_reminder');
		$this->dropColumn('{{%issue_stage_type}}', 'calendar_background');

		$this->addColumn('{{%issue_type}}', 'provision_type', $this->smallInteger());
		$this->addColumn('{{%issue_type}}', 'meet', $this->boolean());

		$this->dropColumn('{{%issue_type}}', 'parent_id');
	}

}

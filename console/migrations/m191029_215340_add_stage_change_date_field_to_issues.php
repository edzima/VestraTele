<?php

use yii\db\Migration;

/**
 * Class m191029_215340_add_status_change_date_field_to_issues
 */
class m191029_215340_add_stage_change_date_field_to_issues extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue}}', 'stage_change_at', $this->dateTime());
		$this->addColumn('{{%issue_stage}}', 'days_reminder', $this->integer());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%issue}}', 'stage_change_at');
		$this->dropColumn('{{%issue_stage}}', 'days_reminder');
	}

}

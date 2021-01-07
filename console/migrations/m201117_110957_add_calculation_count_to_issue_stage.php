<?php

use console\base\Migration;

/**
 * Class m201117_110957_stage_calculation
 */
class m201117_110957_add_calculation_count_to_issue_stage extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue_stage_type}}', 'min_calculation_count', $this->smallInteger()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%issue_stage_type}}', 'min_calculation_count');
	}
	
}

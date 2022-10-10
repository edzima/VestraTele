<?php

use console\base\Migration;

/**
 * Class m220922_124116_summon_dates_indexes
 */
class m220922_124116_summon_dates_indexes extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createIndex('{{%index_summon_realize_at}}', '{{%summon}}', 'realize_at');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropIndex('{{%index_summon_realize_at}}', '{{%summon}}');
	}

}

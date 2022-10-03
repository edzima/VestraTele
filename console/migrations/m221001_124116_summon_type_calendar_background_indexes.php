<?php

use console\base\Migration;

/**
 * Class m221001_124116_summon_type_calendar_background_indexes
 */
class m221001_124116_summon_type_calendar_background_indexes extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%summon_type}}', '{{%calendar_background}}', $this->string()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%summon_type}}', '{{%calendar_background}}');
	}

}

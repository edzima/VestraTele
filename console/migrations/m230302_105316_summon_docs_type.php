<?php

use console\base\Migration;

/**
 * Class m230302_105316_summon_docs_type
 */
class m230302_105316_summon_docs_type extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%summon_doc}}', 'summon_types', $this->string()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%summon_doc}}', 'summon_types');
	}

}

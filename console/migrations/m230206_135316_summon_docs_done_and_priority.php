<?php

use console\base\Migration;

/**
 * Class m230206_135316_summon_docs_done_and_priority
 */
class m230206_135316_summon_docs_done_and_priority extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {

		$this->addColumn(
			'{{%summon_doc}}',
			'priority',
			$this->smallInteger()->null()
		);

		$this->addColumn(
			'{{%summon_doc_list}}',
			'done_at',
			$this->dateTime()->null()
		);

		$this->createIndex(
			'{{%summon_doc_priority}}',
			'{{%summon_doc}}',
			'priority'
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropIndex(
			'{{%summon_doc_priority}}',
			'{{%summon_doc}}'
		);
		$this->dropColumn(
			'{{%summon_doc}}',
			'priority'
		);

		$this->dropColumn(
			'{{%summon_doc_list}}',
			'done_at'
		);
	}

}

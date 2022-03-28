<?php

use console\base\Migration;

/**
 * Class m220322_120416_issue_relation
 */
class m280322_135316_summon_docs extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {

		$this->createTable('{{%summon_doc}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string(50)->notNull()->unique(),
		]);

		$this->addColumn('{{%summon}}', 'doc_type_id', $this->integer()->null());

		$this->addForeignKey('{{%fk_summon_doc_type}}',
			'{{%summon}}', 'doc_type_id',
			'{{%summon_doc}}', 'id',
			'CASCADE',
			'CASCADE'
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropForeignKey('{{%fk_summon_doc_type}}', '{{%summon}}');
		$this->dropColumn('{{%summon}}', 'doc_type_id');
		$this->dropTable('{{%summon_doc}}');
	}

}

<?php

use console\base\Migration;

/**
 * Class m220322_120416_issue_relation
 */
class m220323_124316_summon_docs_multiple extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {

		$this->dropForeignKey('{{%fk_summon_doc_type}}', '{{%summon}}');
		$this->dropColumn('{{%summon}}', 'doc_type_id');

		$this->createTable('{{%summon_doc_list}}', [
			'summon_id' => $this->integer()->notNull(),
			'doc_type_id' => $this->integer()->notNull(),
		]);

		$this->addPrimaryKey('{{%PK_summon_doc_list}}', '{{%summon_doc_list}}',
			['summon_id', 'doc_type_id']
		);

		$this->addForeignKey('{{%fk_summon_doc_list_summon}}',
			'{{%summon_doc_list}}', 'summon_id',
			'{{%summon}}', 'id',
			'CASCADE',
			'CASCADE'
		);

		$this->addForeignKey('{{%fk_summon_doc_list_doc}}',
			'{{%summon_doc_list}}', 'doc_type_id',
			'{{%summon_doc}}', 'id',
			'CASCADE',
			'CASCADE'
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%summon_doc_list}}');

		$this->addColumn('{{%summon}}', 'doc_type_id', $this->integer()->null());

		$this->addForeignKey('{{%fk_summon_doc_type}}',
			'{{%summon}}', 'doc_type_id',
			'{{%summon_doc}}', 'id',
			'CASCADE',
			'CASCADE'
		);
	}

}

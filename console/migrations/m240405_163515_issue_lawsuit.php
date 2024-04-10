<?php

use console\base\Migration;

/**
 * Class m240405_163515_issue_lawsuit
 *
 */
class m240405_163515_issue_lawsuit extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('{{%lawsuit}}', [
			'id' => $this->primaryKey(),
			'court_id' => $this->integer()->notNull(),
			'signature_act' => $this->string()->null(),
			'details' => $this->text()->null(),
			'room' => $this->string()->null(),
			'due_at' => $this->dateTime()->null(),
			'created_at' => $this->timestamp()->notNull(),
			'updated_at' => $this->timestamp()->notNull(),
			'creator_id' => $this->integer()->notNull(),
			'location' => $this->char(2)->null(),
		]);

		$this->createTable('{{%lawsuit_issue}}', [
			'issue_id' => $this->integer()->notNull(),
			'lawsuit_id' => $this->integer()->null(),
			'created_at' => $this->timestamp()->notNull(),
			'updated_at' => $this->timestamp()->notNull(),
		]);

		$this->addForeignKey('{{%fk_lawsuit_issue_issue}}',
			'{{%lawsuit_issue}}', 'issue_id',
			'{{%issue}}', 'id',
			'CASCADE', 'CASCADE'
		);

		$this->addForeignKey('{{%fk_lawsuit_issue_lawsuit}}',
			'{{%lawsuit_issue}}', 'lawsuit_id',
			'{{%lawsuit}}', 'id',
			'CASCADE', 'CASCADE'
		);

		$this->addForeignKey('{{%fk_lawsuit_court}}',
			'{{%lawsuit}}', 'court_id',
			'{{%court}}', 'id',
			'CASCADE', 'CASCADE'
		);

		$this->addForeignKey('{{%fk_lawsuit_creator_id}}',
			'{{%lawsuit}}', 'creator_id',
			'{{%user}}', 'id',
			'CASCADE', 'CASCADE'
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%lawsuit_issue}}');
		$this->dropTable('{{%lawsuit}}');
	}
}

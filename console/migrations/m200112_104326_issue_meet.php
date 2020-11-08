<?php

use yii\db\Migration;

/**
 * Class m200112_104326_issue_meet
 */
class m200112_104326_issue_meet extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {

		$this->createTable('{{%issue_meet}}', [
			'id' => $this->primaryKey(),
			'type_id' => $this->integer()->notNull(),
			'phone' => $this->string(20)->notNull(),
			'client_name' => $this->string(20)->notNull(),
			'client_surname' => $this->string(30),
			'tele_id' => $this->integer(),
			'agent_id' => $this->integer(),
			'created_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
			'updated_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
			'date_at' => $this->dateTime(),
			'date_end_at' => $this->dateTime(),
			'details' => $this->text(),
			'status' => $this->integer()->notNull(),
			'city_id' => $this->integer(),
			'sub_province_id' => $this->integer(),
			'street' => $this->string(50),
			'email' => $this->string(100),

		]);
		$this->addForeignKey('{{%fk_issue_meet_tele}}', '{{%issue_meet}}', 'tele_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_issue_meet_agent}}', '{{%issue_meet}}', 'agent_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_issue_meet_type}}', '{{%issue_meet}}', 'type_id', '{{%issue_type}}', 'id', 'CASCADE', 'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropForeignKey('{{%fk_issue_meet_tele}}', '{{%issue_meet}}');
		$this->dropForeignKey('{{%fk_issue_meet_agent}}', '{{%issue_meet}}');
		$this->dropForeignKey('{{%fk_issue_meet_type}}', '{{%issue_meet}}');
		$this->dropForeignKey('{{%fk_issue_meet_city}}', '{{%issue_meet}}');

		$this->dropTable('{{%issue_meet}}');
	}

}

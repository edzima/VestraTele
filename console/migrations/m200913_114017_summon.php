<?php

use console\base\Migration;

/**
 * Class m200913_114017_summon
 */
class m200913_114017_summon extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {

		$this->createTable('{{%summon}}', [
			'id' => $this->primaryKey(),
			'type' => $this->smallInteger()->notNull(),
			'status' => $this->smallInteger()->notNull(),
			'term' => $this->smallInteger(),
			'entity_id' => $this->integer()->notNull(),
			'city_id' => $this->integer()->notNull(),
			'title' => $this->string(255)->notNull(),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
			'start_at' => $this->dateTime()->notNull(),
			'realize_at' => $this->dateTime(),
			'realized_at' => $this->dateTime(),
			'issue_id' => $this->integer()->notNull(),
			'owner_id' => $this->integer()->notNull(),
			'contractor_id' => $this->integer()->notNull(),
		]);

		$this->addColumn('{{%issue_entity_responsible}}', 'is_for_summon', $this->boolean());

		$this->addForeignKey('{{%fk_summon_issue}}', '{{%summon}}', 'issue_id', '{{%issue}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_summon_user_owner}}', '{{%summon}}', 'owner_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_summon_user_contractor}}', '{{%summon}}', 'contractor_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_summon_entity}}', '{{%summon}}', 'entity_id', '{{%issue_entity_responsible}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_summon_entity_city}}', '{{%summon}}', 'city_id', '{{%teryt_simc}}', 'id', 'CASCADE', 'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%issue_entity_responsible}}', 'is_for_summon');

		$this->dropForeignKey('{{%fk_summon_entity}}', '{{%summon}}');
		$this->dropForeignKey('{{%fk_summon_entity_city}}', '{{%summon}}');
		$this->dropForeignKey('{{%fk_summon_issue}}', '{{%summon}}');
		$this->dropForeignKey('{{%fk_summon_user_owner}}', '{{%summon}}');
		$this->dropForeignKey('{{%fk_summon_user_contractor}}', '{{%summon}}');
		$this->dropTable('{{%summon}}');
	}
}

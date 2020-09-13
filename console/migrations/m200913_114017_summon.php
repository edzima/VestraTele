<?php

use yii\db\Migration;

/**
 * Class m200913_114017_summon
 */
class m200913_114017_summon extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp()
	{
		$this->createTable('{{%summon}}', [
			'id' => $this->primaryKey(),
			'status' => $this->smallInteger()->notNull(),
			'title' => $this->string(255)->notNull(),
			'created_at' => $this->integer(),
			'updated_at' => $this->integer(),
			'realized_at' => $this->integer(),
			'issue_id' => $this->integer()->notNull(),
			'owner_id' => $this->integer()->notNull(),
			'contractor_id' => $this->integer()->notNull()
		]);

		$this->addForeignKey('{{%fk_summon_issue}}', '{{%summon}}', 'issue_id', '{{%issue}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_summon_user_owner}}', '{{%summon}}', 'owner_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('{{%fk_summon_user_contractor}}', '{{%summon}}', 'contractor_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');

	}

	/**
	 * {@inheritdoc}0
	 */
	public function safeDown()
	{
		$this->dropForeignKey('{{%fk_summon_issue}}', '{{%summon}}');
		$this->dropForeignKey('{{%fk_summon_user_owner}}', '{{%summon}}');
		$this->dropForeignKey('{{%fk_summon_user_contractor}}', '{{%summon}}');
		$this->dropTable('{{%summon}}');
	}
}

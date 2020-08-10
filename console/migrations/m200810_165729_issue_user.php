<?php

use console\base\Migration;

/**
 * Class m200810_165729_issue_user
 */
class m200810_165729_issue_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->createTable('{{%issue_user}}', [
			'user_id' => $this->integer()->notNull(),
			'issue_id' => $this->integer()->notNull(),
			'type' => $this->string()->notNull(),
		]);


		$this->addForeignKey('fk_issue_user_user', '{{%issue_user}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('fk_issue_user_issue', '{{%issue_user}}', 'issue_id', '{{%issue}}', 'id', 'CASCADE', 'CASCADE');
		$this->addPrimaryKey('{{%pk_user_address}}', '{{%issue_user}}', ['user_id', 'issue_id', 'type']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropTable('{{%issue_user}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200810_165729_issue_user cannot be reverted.\n";

        return false;
    }
    */
}

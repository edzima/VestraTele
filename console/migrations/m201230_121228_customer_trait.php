<?php

use yii\db\Migration;

/**
 * Class m201230_121228_customer_trait
 */
class m201230_121228_customer_trait extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->createTable('{{%customer_trait}}', [
			'user_id' => $this->integer()->notNull(),
			'trait_id' => $this->integer()->notNull(),
		]);

		$this->addPrimaryKey('{{%pk_user_trait}}', '{{%customer_trait}}', ['user_id', 'trait_id']);
		$this->addForeignKey('{{%fk_user_user_trait}}', '{{%customer_trait}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->createIndex('{{%index_customer_trait}}', '{{%customer_trait}}', 'trait_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropForeignKey('{{%fk_user_user_trait}}', '{{%customer_trait}}');
		$this->dropTable('{{%customer_trait}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201230_121228_customer_trait cannot be reverted.\n";

        return false;
    }
    */
}

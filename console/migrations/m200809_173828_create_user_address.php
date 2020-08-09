<?php

use yii\db\Migration;

/**
 * Class m200809_173828_create_user_address
 */
class m200809_173828_create_user_address extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
		}


		$this->createTable('{{%user_address}}', [
			'city_id' => $this->integer()->notNull(),
			'type' => $this->string()->notNull(),
			'subservience_id' => $this->integer()->notNull(),
			'street' => $this->string()->notNull(),
			'city_code' => $this->integer()->notNull()
		], $tableOptions);


		$this->addForeignKey('fk_useradress_user', '{{%user_address}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');



	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200809_173828_create_user_address cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200809_173828_create_user_address cannot be reverted.\n";

        return false;
    }
    */
}

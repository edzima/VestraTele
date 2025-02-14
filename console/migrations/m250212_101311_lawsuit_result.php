<?php

use console\base\Migration;

/**
 * Class m250212_101311_lawsuit_result
 */
class m250212_101311_lawsuit_result extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%lawsuit}}', 'result', $this->string()->null());
		$this->addColumn('{{%lawsuit}}', 'spi_last_sync_at', $this->dateTime()->null());
		$this->addColumn('{{%lawsuit}}', 'spi_last_update_at', $this->dateTime()->null());
		$this->addColumn('{{%lawsuit}}', 'spi_confirmed_user', $this->integer()->null());
		$this->addForeignKey('{{%FK_lawsuit_spi_confirmed_user}}',
			'{{%lawsuit}}', 'spi_confirmed_user',
			'{{%user}}', 'id',
			'NO ACTION', 'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropForeignKey('{{%FK_lawsuit_spi_confirmed_user}}', '{{%lawsuit}}');
		$this->dropColumn('{{%lawsuit}}', 'result');
		$this->dropColumn('{{%lawsuit}}', 'spi_last_update_at');
		$this->dropColumn('{{%lawsuit}}', 'spi_confirmed_user');
		$this->dropColumn('{{%lawsuit}}', 'spi_last_sync_at');
	}

}

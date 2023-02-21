<?php

use console\base\Migration;

/**
 * Class m230221_135316_summon_updater_id
 */
class m230221_135316_summon_updater_id extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {

		$this->addColumn(
			'{{%summon}}',
			'updater_id',
			$this->integer()->null()
		);

		$this->addForeignKey(
			'{{%FK_summon_updater}}',
			'{{%summon}}',
			'updater_id',
			'{{%user}}',
			'id',
			null,
			'CASCADE'
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {

		$this->dropForeignKey(
			'{{%FK_summon_updater}}',
			'{{%summon}}',
		);
		$this->dropColumn(
			'{{%summon}}',
			'updater_id'
		);
	}

}

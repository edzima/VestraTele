<?php

use console\base\Migration;

/**
 * Class m241021_161511_lawsuit_rul
 */
class m250129_125611_lawsuit_signature extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn('{{%lawsuit}}', 'signature_act', $this->string()->notNull());
		$this->createIndex('{{%lawsuit_signature_court}}',
			'{{%lawsuit}}',
			['signature_act', 'court_id'], true);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropIndex('{{%lawsuit_signature_court}}', '{{%lawsuit}}');
		$this->alterColumn('{{%lawsuit}}', 'signature_act', $this->string()->null());
	}

}

<?php

use console\base\Migration;

/**
 * Class m210319_113547_drop_only_with_tele_column_from_provision_type
 */
class m210319_113547_drop_only_with_tele_column_from_provision_type extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->dropColumn('{{%provision_type}}', 'only_with_tele');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->addColumn('{{%provision_type}}', 'only_with_tele', $this->boolean());
	}

}

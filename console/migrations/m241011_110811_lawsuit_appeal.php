<?php

use console\base\Migration;

/**
 * Class m240826_162401_lead_status_deal_stage
 */
class m241011_110811_lawsuit_appeal extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%lawsuit}}', 'is_appeal', $this->boolean()->notNull()->defaultValue(0));
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%lawsuit}}', 'is_appeal');
	}

}

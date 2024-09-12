<?php

use console\base\Migration;

/**
 * Class m240418_140915_leads_chart
 *
 */
class m240418_140915_leads_chart extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%lead_status}}', 'chart_color', $this->string()->null());
		$this->addColumn('{{%lead_status}}', 'chart_group', $this->string()->null());

	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%lead_status}}', 'chart_color');
		$this->dropColumn('{{%lead_status}}', 'chart_group');
	}
}

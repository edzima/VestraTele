<?php

use console\base\Migration;

/**
 * Class m220520_124316_lead_campaign_url_referer
 */
class m220520_124316_lead_campaign_url_referer extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp(): void {
		$this->addColumn('{{%lead_campaign}}', 'url_search_part', $this->string()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown(): void {
		$this->dropColumn('{{%lead_campaign}}', 'url_search_part');
	}

}

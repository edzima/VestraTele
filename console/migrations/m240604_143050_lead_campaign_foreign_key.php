<?php

use console\base\Migration;

/**
 * Class m240506_105550_leads_user_activity
 *
 */
class m240604_143050_lead_campaign_foreign_key extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp(): void {
		$this->dropForeignKey('{{%fk_lead_campaign}}', '{{%lead}}');
		$this->addForeignKey('{{%fk_lead_campaign}}',
			'{{%lead}}',
			'campaign_id',
			'{{%lead_campaign}}',
			'id',
			'SET NULL',
			'CASCADE'
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown(): void {
		$this->dropForeignKey('{{%fk_lead_campaign}}', '{{%lead}}');
		$this->addForeignKey('{{%fk_lead_campaign}}',
			'{{%lead}}',
			'campaign_id',
			'{{%lead_campaign}}',
			'id',
			'CASCADE',
			'CASCADE'
		);
	}
}

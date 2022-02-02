<?php

use console\base\Migration;

/**
 * Class m220113_131816_lead_source-dialer_phone
 */
class m220125_114316_user_profile_email_issue_visibility extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%user_profile}}', 'email_hidden_in_frontend_issue', $this->boolean());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%user_profile}}', 'email_hidden_in_frontend_issue');
	}

}

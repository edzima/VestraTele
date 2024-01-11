<?php

use console\base\Migration;

/**
 * Class m240111_094012_issue_entity_agreement
 */
class m240111_094012_issue_entity_agreement extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%issue}}', 'entity_agreement_details', $this->string(255)->null());
		$this->addColumn('{{%issue}}', 'entity_agreement_at', $this->date()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%issue}}', 'entity_agreement_details');
		$this->dropColumn('{{%issue}}', 'entity_agreement_at');
	}
}

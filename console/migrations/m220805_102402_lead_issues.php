<?php

use common\modules\lead\Module;
use console\base\Migration;

/**
 * Class m220805_102402_lead_issues
 */
class m220805_102402_lead_issues extends Migration {

	public function init() {
		parent::init();
		$this->db = Module::getInstance()->db;
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {

		$this->createTable('{{%lead_crm}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull()->unique(),
			'backend_url' => $this->string()->notNull()->unique(),
			'frontend_url' => $this->string()->notNull()->unique(),
		]);

		$this->createTable('{{%lead_issue}}', [
			'lead_id' => $this->integer()->notNull(),
			'issue_id' => $this->integer()->notNull(),
			'crm_id' => $this->integer()->notNull(),
			'created_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
			'updated_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
			'confirmed_at' => $this->timestamp()->null(),
		]);

		$this->addPrimaryKey('{{%PK_lead_issue}}', '{{%lead_issue}}',
			[
				'lead_id',
				'issue_id',
				'crm_id',
			]
		);

		$this->addForeignKey('{{%fk_lead_issue_lead}}', '{{%lead_issue}}', 'lead_id', '{{%lead}}', 'id');
		$this->addForeignKey('{{%fk_lead_issue_crm}}', '{{%lead_issue}}', 'crm_id', '{{%lead_crm}}', 'id');
		$this->createIndex('{{%index_lead_issue_issue}}', '{{%lead_issue}}', 'issue_id');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%lead_issue}}');
		$this->dropTable('{{%lead_crm}}');
	}

}

<?php

use console\base\Migration;

class m190218_135218_issue extends Migration {

	public function safeUp() {


		$this->createTable('{{%issue_stage}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull()->unique(),
			'short_name' => $this->string()->notNull()->unique(),
			'posi' => $this->integer()->unsigned()->notNull()->defaultValue(0),
			'days_reminder' => $this->integer(),
		]);

		$this->createTable('{{%issue_entity_responsible}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull()->unique(),
		]);

		$this->createTable('{{%issue_type}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull()->unique(),
			'short_name' => $this->string()->notNull()->unique(),
			'provision_type' => $this->smallInteger()->notNull()->defaultValue(1),
			'meet' => $this->boolean(),
		]);

		$this->createTable('{{%issue_stage_type}}', [
			'type_id' => $this->integer()->notNull(),
			'stage_id' => $this->integer()->notNull(),
		]);

		$this->addForeignKey('issue_stage_type_type', '{{%issue_stage_type}}', 'type_id', '{{%issue_type}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('issue_stage_type_stage', '{{%issue_stage_type}}', 'stage_id', '{{%issue_stage}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%issue}}', [
			'id' => $this->primaryKey(),
			'client_first_name' => $this->string()->notNull(),
			'client_surname' => $this->string()->notNull(),
			'client_phone_1' => $this->string(20),
			'client_phone_2' => $this->string(20),
			'client_email' => $this->string(),
			'client_city_id' => $this->integer()->notNull(),
			'client_subprovince_id' => $this->integer(),
			'client_city_code' => $this->string(6)->notNull(),
			'client_street' => $this->string()->notNull(),
			'victim_first_name' => $this->string(),
			'victim_surname' => $this->string(),
			'victim_phone' => $this->string(20),
			'victim_email' => $this->string(),
			'victim_city_id' => $this->integer(),
			'victim_subprovince_id' => $this->integer(),
			'victim_city_code' => $this->string(6),
			'victim_street' => $this->string(),
			'details' => $this->text(),
			'provision_type' => $this->smallInteger()->notNull(),
			'provision_value' => $this->decimal(10, 2),
			'provision_base' => $this->decimal(10, 2),
			'stage_id' => $this->integer()->notNull(),
			'type_id' => $this->integer()->notNull(),
			'entity_responsible_id' => $this->integer()->notNull(),
			'archives_nr' => $this->string(10),
			'payed' => $this->boolean()->notNull()->defaultValue(false),
			'lawyer_id' => $this->integer()->notNull(),
			'agent_id' => $this->integer()->notNull(),
			'tele_id' => $this->integer(),
			'created_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
			'updated_at' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
			'date' => $this->timestamp()->notNull()->defaultExpression('current_timestamp()'),
			'accident_at' => $this->timestamp()->defaultValue(null),
			'stage_change_at' => $this->timestamp()->defaultValue(null),
		]);

		$this->addForeignKey('fk_issue_entity_responsible', '{{%issue}}', 'entity_responsible_id', '{{%issue_entity_responsible}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('fk_issue_stage', '{{%issue}}', 'stage_id', '{{%issue_stage}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('fk_issue_type', '{{%issue}}', 'type_id', '{{%issue_type}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('fk_issue_client_agent', '{{%issue}}', 'agent_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('fk_issue_lawyer', '{{%issue}}', 'lawyer_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('fk_issue_tele', '{{%issue}}', 'tele_id', '{{%user}}', 'id');
	}

	public function safeDown() {

		$this->dropForeignKey('issue_stage_type_type', '{{%issue_stage_type}}');
		$this->dropForeignKey('issue_stage_type_stage', '{{%issue_stage_type}}');

		$this->dropForeignKey('fk_issue_entity_responsible', '{{%issue}}');
		$this->dropForeignKey('fk_issue_stage', '{{%issue}}');

		$this->dropForeignKey('fk_issue_type', '{{%issue}}');
		$this->dropForeignKey('fk_issue_client_agent', '{{%issue}}');
		$this->dropForeignKey('fk_issue_lawyer', '{{%issue}}');
		$this->dropForeignKey('fk_issue_tele', '{{%issue}}');

		$this->dropTable('{{%issue_stage_type}}');
		$this->dropTable('{{%issue_type}}');
		$this->dropTable('{{%issue_stage}}');
		$this->dropTable('{{%issue_entity_responsible}}');
		$this->dropTable('{{%issue}}');
	}

}

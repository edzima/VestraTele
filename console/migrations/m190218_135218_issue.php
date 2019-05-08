<?php

use console\base\Migration;
use yii\db\Expression;

class m190218_135218_issue extends Migration {

	public function safeUp() {

		$this->createTable('{{%issue_entity_responsible}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull()->unique(),
		]);

		$this->createTable('{{%issue_stage}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull()->unique(),
			'short_name' => $this->string()->notNull()->unique(),
			'posi' => $this->integer()->unsigned()->notNull()->defaultValue(0),
		]);

		$this->createTable('{{%issue_type}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull()->unique(),
			'short_name' => $this->string()->notNull()->unique(),
			'provision_type' => $this->smallInteger()->notNull()->defaultValue(1),
		]);

		$this->createTable('{{%issue_stage_type}}', [
			'type_id' => $this->integer()->notNull(),
			'stage_id' => $this->integer()->notNull(),
		]);

		$this->addForeignKey('issue_stage_type_type', '{{%issue_stage_type}}', 'type_id', '{{%issue_type}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('issue_stage_type_stage', '{{%issue_stage_type}}', 'stage_id', '{{%issue_stage}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%issue}}', [
			'id' => $this->primaryKey(),
			'created_at' => $this->timestamp()->defaultValue(new Expression('CURRENT_TIMESTAMP')),
			'updated_at' => $this->timestamp()->defaultValue(new Expression('CURRENT_TIMESTAMP')),
			'date' => $this->timestamp()->notNull(),
			'agent_id' => $this->integer()->notNull(),
			'client_first_name' => $this->string()->notNull(),
			'client_surname' => $this->string()->notNull(),
			'client_phone_1' => $this->string(15)->notNull(),
			'client_phone_2' => $this->string(15),
			'client_email' => $this->string(),
			'client_city_id' => $this->integer()->notNull(),
			'client_subprovince_id' => $this->integer(),
			'client_city_code' => $this->string(6)->notNull(),
			'client_street' => $this->string()->notNull(),
			'victim_first_name' => $this->string(),
			'victim_surname' => $this->string(),
			'victim_phone' => $this->string(15),
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
			'lawyer_id', $this->integer()->notNull(),
			'tele_id', $this->integer()

		]);
		$this->addForeignKey('fk_issue_client_city', '{{%issue}}', 'client_city_id', '{{%miasta}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('fk_issue_victim_city', '{{%issue}}', 'victim_city_id', '{{%miasta}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('fk_issue_entity_responsible', '{{%issue}}', 'entity_responsible_id', '{{%issue_entity_responsible}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('fk_issue_stage', '{{%issue}}', 'stage_id', '{{%issue_stage}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('fk_issue_type', '{{%issue}}', 'type_id', '{{%issue_type}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('fk_issue_client_agent', '{{%issue}}', 'agent_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('fk_issue_lawyer', '{{%issue}}', 'lawyer_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('fk_issue_tele', '{{%issue}}', 'tele_id', '{{%user}}', 'id');


		$this->createTable('{{%issue_pay}}', [
			'id' => $this->primaryKey(),
			'issue_id' => $this->integer()->notNull(),
			'type' => $this->integer()->notNull(),
			'date' => $this->dateTime()->notNull()->defaultValue(new Expression('CURRENT_TIMESTAMP')),
			'value' => $this->decimal(10, 2)->notNull(),
		]);

		$this->addForeignKey('fk_issue_pay_issue', '{{%issue_pay}}', 'issue_id', '{{%issue}}', 'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%issue_note}}', [
			'id' => $this->primaryKey(),
			'issue_id' => $this->integer()->notNull(),
			'user_id' => $this->integer()->notNull(),
			'title' => $this->string()->notNull(),
			'description' => $this->text()->notNull(),
			'created_at' => $this->timestamp()->defaultValue(new Expression('CURRENT_TIMESTAMP')),
			'updated_at' => $this->timestamp()->defaultValue(new Expression('CURRENT_TIMESTAMP')),
		]);
		$this->addForeignKey('fk_issue_note_user', '{{%issue_note}}', 'user_id', '{{%user}}', 'id', 'CASCADE', 'CASCADE');
		$this->addForeignKey('fk_issue_note_issue', '{{%issue_note}}', 'issue_id', '{{%issue}}', 'id', 'CASCADE', 'CASCADE');

		$this->batchInsert('{{%issue_type}}', ['id', 'name', 'short_name'], [
			[
				'id' => 1,
				'name' => 'Wypadek',
				'short_name' => 'W',
			],
			[
				'id' => 2,
				'name' => 'Świadczenia Postępowanie Administracyjne',
				'short_name' => 'SPA',
			],
			[
				'id' => 3,
				'name' => 'Świadczenia Postępowanie Cywilne',
				'short_name' => 'SPC',
			],
			[
				'id' => 4,
				'name' => 'Zwroty prowizji',
				'short_name' => 'ZP',
			],

		]);

		$this->batchInsert('{{%issue_stage}}', ['id', 'name', 'short_name'], [
			[
				'id' => 1,
				'name' => 'Kompletowanie dokumentów',
				'short_name' => 'KD',
			],
			[
				'id' => 2,
				'name' => 'Postępowanie likwidacyjne',
				'short_name' => 'PL',
			],
			[
				'id' => 3,
				'name' => 'Postępowanie odwoławcze',
				'short_name' => 'PO',
			],
			[
				'id' => 4,
				'name' => 'Negocjacje ugodowe',
				'short_name' => 'NU',
			],
			[
				'id' => 5,
				'name' => 'Sąd',
				'short_name' => 'S',
			],
			[
				'id' => 6,
				'name' => 'Archiwum',
				'short_name' => 'A',
			],
			[
				'id' => 7,
				'name' => 'Archiwum tymczasowe',
				'short_name' => 'AT',
			],
			[
				'id' => 8,
				'name' => 'Wniosek',
				'short_name' => 'W',
			],
			[
				'id' => 9,
				'name' => 'Odwołanie',
				'short_name' => 'O',
			],
			[
				'id' => 10,
				'name' => 'Skarga',
				'short_name' => 'WSA',
			],
			[
				'id' => 11,
				'name' => 'Zawezwanie do próby ugodowej',
				'short_name' => 'ZPU',
			],
			[
				'id' => 12,
				'name' => 'Pozew',
				'short_name' => 'P',
			],
			[
				'id' => 13,
				'name' => 'Reklamacja',
				'short_name' => 'R',
			],
			[
				'id' => 14,
				'name' => 'Przekazanie do Kancelarii',
				'short_name' => 'PdK',
			],
			[
				'id' => 15,
				'name' => 'Dokumentacja kompletna',
				'short_name' => 'DK',
			],
			[
				'id' => 16,
				'name' => 'Przygotowanie do archiwum',
				'short_name' => 'PDA',
			],
		]);

		$this->batchInsert('{{%issue_stage_type}}', ['type_id', 'stage_id'], [
			[
				'type_id' => 1,
				'stage_id' => 1,
			],
			[
				'type_id' => 1,
				'stage_id' => 2,
			],
			[
				'type_id' => 1,
				'stage_id' => 3,
			],
			[
				'type_id' => 1,
				'stage_id' => 4,
			],
			[
				'type_id' => 1,
				'stage_id' => 5,
			],
			[
				'type_id' => 1,
				'stage_id' => 6,
			],
			[
				'type_id' => 1,
				'stage_id' => 7,
			],
			[
				'type_id' => 2,
				'stage_id' => 1,
			],
			[
				'type_id' => 2,
				'stage_id' => 8,
			],
			[
				'type_id' => 2,
				'stage_id' => 9,
			],
			[
				'type_id' => 2,
				'stage_id' => 10,
			],
			[
				'type_id' => 2,
				'stage_id' => 6,
			],
			[
				'type_id' => 3,
				'stage_id' => 1,
			],
			[
				'type_id' => 3,
				'stage_id' => 11,
			],
			[
				'type_id' => 3,
				'stage_id' => 12,
			],
			[
				'type_id' => 3,
				'stage_id' => 6,
			],

			[
				'type_id' => 4,
				'stage_id' => 1,
			],
			[
				'type_id' => 4,
				'stage_id' => 13,
			],
			[
				'type_id' => 4,
				'stage_id' => 11,
			],
			[
				'type_id' => 4,
				'stage_id' => 5,
			],
			[
				'type_id' => 4,
				'stage_id' => 6,
			],
			[
				'type_id' => 1,
				'stage_id' => 14,
			],
			[
				'type_id' => 2,
				'stage_id' => 14,
			],
			[
				'type_id' => 3,
				'stage_id' => 14,
			],
			[
				'type_id' => 4,
				'stage_id' => 14,
			],
			[
				'type_id' => 1,
				'stage_id' => 15,
			],
			[
				'type_id' => 2,
				'stage_id' => 15,
			],
			[
				'type_id' => 3,
				'stage_id' => 15,
			],
			[
				'type_id' => 4,
				'stage_id' => 15,
			],
			[
				'type_id' => 1,
				'stage_id' => 16,
			],
			[
				'type_id' => 2,
				'stage_id' => 16,
			],
			[
				'type_id' => 3,
				'stage_id' => 16,
			],
			[
				'type_id' => 4,
				'stage_id' => 16,
			],

		]);
	}

	public function safeDown() {
		$this->dropForeignKey('issue_stage_type_type', '{{%issue_stage_type}}');
		$this->dropForeignKey('issue_stage_type_stage', '{{%issue_stage_type}}');

		$this->dropForeignKey('fk_issue_entity_responsible', '{{%issue}}');
		$this->dropForeignKey('fk_issue_stage', '{{%issue}}');
		$this->dropForeignKey('fk_issue_client_city', '{{%issue}}');
		$this->dropForeignKey('fk_issue_victim_city', '{{%issue}}');
		$this->dropForeignKey('fk_issue_type', '{{%issue}}');
		$this->dropForeignKey('fk_issue_client_agent', '{{%issue}}');
		$this->dropForeignKey('fk_issue_lawyer', '{{%issue}}');
		$this->dropForeignKey('fk_issue_tele', '{{%issue}}');

		$this->dropForeignKey('fk_issue_pay_issue', '{{%issue_pay}}');

		$this->dropTable('{{%issue_pay}}');
		$this->dropTable('{{%issue_stage_type}}');
		$this->dropTable('{{%issue_type}}');
		$this->dropTable('{{%issue_stage}}');
		$this->dropTable('{{%issue_entity_responsible}}');
		$this->dropTable('{{%issue_note}}');
		$this->dropTable('{{%issue}}');
	}

}

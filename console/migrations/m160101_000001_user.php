<?php

use console\base\Migration;

class m160101_000001_user extends Migration {

	public function up() {

		$this->createTable('{{%user}}', [
			'id' => $this->primaryKey(),
			'username' => $this->string()->notNull()->unique(),
			'auth_key' => $this->string(32)->notNull(),
			'access_token' => $this->string(255),
			'password_hash' => $this->string(255)->notNull(),
			'password_reset_token' => $this->string()->unique(),
			'email' => $this->string()->notNull()->unique(),
			'status' => $this->smallInteger()->notNull()->defaultValue(1),
			'ip' => $this->string(128),
			'created_at' => $this->integer()->notNull(),
			'updated_at' => $this->integer()->notNull(),
			'action_at' => $this->integer(),
			'boss' => $this->integer(),
		]);

		$this->createTable('{{%user_profile}}', [
			'user_id' => $this->primaryKey(),
			'firstname' => $this->string(),
			'lastname' => $this->string(),
			'birthday' => $this->integer(),
			'avatar_path' => $this->string(255),
			'gender' => $this->smallInteger(1),
			'website' => $this->string(255),
			'other' => $this->string(),
			'phone' => $this->string(20),
			'phone_2' => $this->string(20),
		]);

		$this->addForeignKey('fk_user', '{{%user_profile}}', 'user_id', '{{%user}}', 'id', 'cascade', 'cascade');
	}

	public function down() {
		$this->dropForeignKey('fk_user', '{{%user_profile}}');

		$this->dropTable('{{%user_profile}}');
		$this->dropTable('{{%user}}');
	}
}

<?php

use console\base\Migration;

class m160101_000006_page extends Migration {

	public function up() {

		$this->createTable('{{%page}}', [
			'id' => $this->primaryKey(),
			'title' => $this->string(255)->notNull(),
			'slug' => $this->string(255)->notNull(),
			'description' => $this->string(255),
			'keywords' => $this->string(255),
			'body' => $this->text()->notNull(),
			'status' => $this->smallInteger()->notNull(),
			'created_at' => $this->integer(),
			'updated_at' => $this->integer(),
		]);
	}

	public function down() {
		$this->dropTable('{{%page}}');
	}
}

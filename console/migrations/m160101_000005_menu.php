<?php

use console\base\Migration;

class m160101_000005_menu extends Migration {

	public function up() {

		$this->createTable('{{%menu}}', [
			'id' => $this->primaryKey(),
			'url' => $this->string(255)->notNull(),
			'label' => $this->string(255)->notNull(),
			'parent_id' => $this->integer(),
			'status' => $this->smallInteger()->notNull(),
			'sort_index' => $this->integer(),
		]);

		$this->addForeignKey('parent', '{{%menu}}', 'parent_id', '{{%menu}}', 'id', 'cascade', 'cascade');
	}

	public function down() {
		$this->dropTable('{{%menu}}');
	}
}

<?php

use console\base\Migration;

/**
 * Class m230313_103522_issue_tag_type
 */
class m230313_103522_issue_tag_type extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('{{%issue_tag_type}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->unique()->notNull(),
			'background' => $this->string(),
			'color' => $this->string(),
			'css_class' => $this->string(),
			'view_issue_position' => $this->string()->null(),
			'issues_grid_position' => $this->string()->null(),
		]);

		$this->insert('{{%issue_tag_type}}', [
			'id' => -10,
			'name' => 'Customer',
			'css_class' => 'label label-danger',
		]);

		$this->insert('{{%issue_tag_type}}', [
			'id' => -100,
			'name' => 'Settlement',
			'css_class' => 'label label-warning',
		]);

		$this->update('{{%issue_tag}}', [
			'type' => -10,
		], [
			'type' => 'client',
		]);

		$this->update('{{%issue_tag}}', [
			'type' => -100,
		], [
			'type' => 'settlement',
		]);

		$this->alterColumn('{{%issue_tag}}', 'type', $this->integer()->null());
		$this->addForeignKey('{{%fk_issue_tag_type}}', '{{%issue_tag}}', 'type', '{{%issue_tag_type}}', 'id', 'SET NULL', 'CASCADE');
		$this->dropIndex('name', '{{%issue_tag}}');

		$this->addColumn('{{%issue_tag_type}}', 'sort_order', $this->smallInteger());
		$this->addColumn('{{%issue_tag_type}}', 'link_issues_grid_position', $this->string()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%issue_tag_type}}', 'sort_order');
		$this->dropColumn('{{%issue_tag_type}}', 'link_issues_grid_position');

		$this->alterColumn('{{%issue_tag}}', 'name', $this->string()->notNull()->unique());
		$this->dropForeignKey('{{%fk_issue_tag_type}}', '{{%issue_tag}}');
		$this->alterColumn('{{%issue_tag}}', 'type', $this->string()->null());

		$this->update('{{%issue_tag}}', [
			'type' => 'client',
		], [
			'type' => -10,
		]);

		$this->update('{{%issue_tag}}', [
			'type' => 'settlement',
		], [
			'type' => -100,
		]);

		$this->dropTable('{{%issue_tag_type}}');
	}

}

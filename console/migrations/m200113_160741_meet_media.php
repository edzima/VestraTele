<?php

use console\base\Migration;

/**
 * Class m200113_160741_meet_media
 */
class m200113_160741_meet_media extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {


		$this->createTable('{{%campaign}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string(50)->notNull()->unique(),
		]);

		$this->insert('{{%campaign}}', ['id' => 1, 'name' => 'Facebook Maciej']);
		$this->addColumn('{{%issue_meet}}', 'campaign_id', $this->integer()->notNull());
		$this->update('{{%issue_meet}}', ['campaign_id' => 1]);

		$this->addForeignKey('{{%fk_issue_meet_campaign}}', '{{%issue_meet}}', 'campaign_id', '{{%campaign}}', 'id', 'CASCADE', 'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropForeignKey('{{%fk_issue_meet_campaign}}', '{{%issue_meet}}');

		$this->dropColumn('{{%issue_meet}}', 'campaign_id');
		$this->dropTable('{{%campaign}}');
	}

}

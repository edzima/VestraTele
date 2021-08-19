<?php

use console\base\Migration;

/**
 * Class m210212_094113_provision_type_active_and_hierarchy
 */
class m210212_094113_provision_new_features extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn('{{%provision_type}}', 'name', $this->string(50)->notNull()->unique());
		$this->addColumn('{{%provision_type}}', 'is_active', $this->boolean()->defaultValue(0));

		//@todo fix restore foreign keys for user columns
		$this->execute('SET FOREIGN_KEY_CHECKS=0;');
		$this->dropPrimaryKey('{{%pk_provision_user}}', '{{%provision_user}}');
		$this->addColumn('{{%provision_user}}', 'id', $this->primaryKey());
		$this->execute('SET FOREIGN_KEY_CHECKS=1;');

		$this->dropColumn('{{%provision_type}}', 'date_from');
		$this->dropColumn('{{%provision_type}}', 'date_to');
		$this->addColumn('{{%provision_type}}', 'from_at', $this->date()->null());
		$this->addColumn('{{%provision_type}}', 'to_at', $this->date()->null());

		$this->addColumn('{{%provision_user}}', 'from_at', $this->date()->null());
		$this->addColumn('{{%provision_user}}', 'to_at', $this->date()->null());
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->execute('SET FOREIGN_KEY_CHECKS=0;');
		$this->dropColumn('{{%provision_user}}', 'id');
		$this->addPrimaryKey('{{%pk_provision_user}}', '{{%provision_user}}', ['from_user_id', 'to_user_id', 'type_id']);
		$this->execute('SET FOREIGN_KEY_CHECKS=1;');

		$this->dropColumn('{{%provision_user}}', 'from_at');
		$this->dropColumn('{{%provision_user}}', 'to_at');

		$this->dropColumn('{{%provision_type}}', 'from_at');
		$this->dropColumn('{{%provision_type}}', 'to_at');
		$this->addColumn('{{%provision_type}}', 'date_from', $this->timestamp()->null());
		$this->addColumn('{{%provision_type}}', 'date_to', $this->timestamp()->null());

		$this->dropColumn('{{%provision_type}}', 'is_active');
	}

}

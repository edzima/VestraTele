<?php

use yii\db\Migration;

/**
 * Class m201001_141321_change_not_null_issue_columns
 */
class m201001_141321_change_not_null_issue_columns extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn('{{%issue}}', 'client_first_name', $this->string()->null());
		$this->alterColumn('{{%issue}}', 'client_surname', $this->string()->null());
		$this->alterColumn('{{%issue}}', 'client_city_id', $this->integer()->null());
		$this->alterColumn('{{%issue}}', 'client_city_code', $this->string()->null());
		$this->alterColumn('{{%issue}}', 'client_street', $this->string()->null());
		$this->alterColumn('{{%issue}}', 'client_street', $this->string()->null());
		$this->alterColumn('{{%issue}}', 'provision_type', $this->smallInteger()->null());
		$this->alterColumn('{{%issue}}','lawyer_id', $this->integer()->null());
		$this->alterColumn('{{%issue}}','agent_id', $this->integer()->null());

	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->alterColumn('{{%issue}}', 'client_first_name', $this->string()->notNull());
		$this->alterColumn('{{%issue}}', 'client_surname', $this->string()->notNull());
		$this->alterColumn('{{%issue}}', 'client_city_id', $this->integer()->notNull());
		$this->alterColumn('{{%issue}}', 'client_city_code', $this->string()->notNull());
		$this->alterColumn('{{%issue}}', 'client_street', $this->string()->notNull());
		$this->alterColumn('{{%issue}}', 'provision_type', $this->smallInteger()->notNull());
		$this->alterColumn('{{%issue}}','lawyer_id', $this->integer()->notNull());
		$this->alterColumn('{{%issue}}','agent_id', $this->integer()->notNull());
	}

}

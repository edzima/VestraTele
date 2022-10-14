<?php

use console\base\Migration;

/**
 * Class m221011_124116_summon_type_options
 */
class m221011_124116_summon_type_options extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%summon_type}}', '{{%options}}', $this->json()->null());
		$this->alterColumn('{{%summon}}', 'city_id', $this->integer()->null());
		$this->alterColumn('{{%summon}}', 'contractor_id', $this->integer()->null());
		$this->alterColumn('{{%summon}}', 'entity_id', $this->integer()->null());
		$this->dropColumn('{{%summon_type}}', 'title');
		$this->dropColumn('{{%summon_type}}', 'term');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->addColumn('{{%summon_type}}', '{{%title}}', $this->string()->null());
		$this->addColumn('{{%summon_type}}', '{{%term}}', $this->string()->null());

		$this->dropColumn('{{%summon_type}}', '{{%options}}');
		$this->alterColumn('{{%summon}}', 'city_id', $this->integer()->notNull());
		$this->alterColumn('{{%summon}}', 'contractor_id', $this->integer()->notNull());
		$this->alterColumn('{{%summon}}', 'entity_id', $this->integer()->notNull());
	}

}

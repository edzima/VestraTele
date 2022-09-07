<?php

use console\base\Migration;

/**
 * Class m211118_131816_summon_type
 */
class m211118_131816_summon_type extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {

		$this->createTable('{{%summon_type}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string(100)->unique()->notNull(),
			'short_name' => $this->string(10)->unique()->notNull(),
			'title' => $this->string(255)->null(),
			'term' => $this->smallInteger()->null(),
		]);

		$this->insertStaticTypes();
		$this->alterColumn('{{%summon}}', 'type', $this->integer()->notNull());
		$this->renameColumn('{{%summon}}', 'type', 'type_id');

		$this->addForeignKey('{{%fk_summon_type}}', '{{%summon}}', 'type_id', '{{%summon_type}}', 'id', 'CASCADE', 'CASCADE');
	}

	private function insertStaticTypes(): void {
		$oldStaticTypes = [
			10 => Yii::t('common', 'Appeal'),
			15 => Yii::t('common', 'Incomplete documentation'),
			20 => Yii::t('common', 'Summon Phonable'),
			30 => Yii::t('common', 'Antyvindication'),
			40 => Yii::t('common', 'Resignation'),
			50 => Yii::t('common', 'Urgency'),
		];
		$rows = [];
		foreach ($oldStaticTypes as $typeId => $name) {
			$shortName = strtoupper(substr($name, 0, 3));
			$rows[] = ['id' => $typeId, 'name' => $name, 'short_name' => $shortName];
		}
		$this->batchInsert('{{%summon_type}}', ['id', 'name', 'short_name'], $rows);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropForeignKey('{{%fk_summon_type}}', '{{%summon}}');
		$this->dropTable('{{%summon_type}}');
		$this->renameColumn('{{%summon}}', 'type_id', 'type');

		$this->alterColumn('{{%summon}}', 'type', $this->smallInteger()->notNull());
	}

}

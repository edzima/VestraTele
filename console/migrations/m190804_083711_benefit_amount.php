<?php

use console\base\Migration;

/**
 * Class m190804_083711_benefit_amount
 */
class m190804_083711_benefit_amount extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {

		$this->createTable('{{%benefit_amount}}', [
			'id' => $this->primaryKey(),
			'type' => $this->smallInteger()->notNull()->unsigned(),
			'from_at' => $this->dateTime()->notNull(),
			'to_at' => $this->dateTime()->notNull(),
			'value' => $this->decimal(10, 2)->notNull(),
		]);


		$this->batchInsert('{{%benefit_amount}}',
			[
				'type',
				'value',
				'from_at',
				'to_at',
			],
			[
				[
					'type' => 1,
					'value' => 520,
					'from_at' => '2016-01-01',
					'to_at' => '2018-10-31',
				],
				[
					'type' => 1,
					'value' => 620,
					'from_at' => '2018-11-01',
					'to_at' => '2019-12-31',
				],
				[
					'type' => 2,
					'value' => 1300,
					'from_at' => '2016-01-01',
					'to_at' => '2016-12-31',
				],
				[
					'type' => 2,
					'value' => 1406,
					'from_at' => '2017-01-01',
					'to_at' => '2017-12-31',
				],
				[
					'type' => 2,
					'value' => 1477,
					'from_at' => '2018-01-01',
					'to_at' => '2018-12-31',
				],
				[
					'type' => 2,
					'value' => 1583,
					'from_at' => '2019-01-01',
					'to_at' => '2019-12-31',
				],

			]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable('{{%benefit_amount}}');
	}

}

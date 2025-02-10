<?php

use console\base\Migration;
use yii\db\Query;

/**
 * Class m241021_161511_lawsuit_rul
 */
class m250127_130311_lawsuit_sessions extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp(): void {
		$this->createTable('{{%lawsuit_sessions}}', [
			'id' => $this->primaryKey(),
			'details' => $this->text(),
			'lawsuit_id' => $this->integer()->notNull(),
			'date_at' => $this->dateTime()->notNull(),
			'created_at' => $this->dateTime()->notNull(),
			'updated_at' => $this->dateTime()->notNull(),
			'room' => $this->string()->null(),
			'is_cancelled' => $this->boolean()->defaultValue(false),
			'presence_of_the_claimant' => $this->smallInteger(),
			'location' => $this->string(),
			'url' => $this->string(),
			'judge' => $this->string(),
			'result' => $this->string(),
		]);

		$this->addForeignKey('{{%FK_lawsuit_sessions_lawsuit}}',
			'{{%lawsuit_sessions}}', 'lawsuit_id',
			'{{%lawsuit}}', 'id',
			'CASCADE',
			'CASCADE'
		);

		$this->insertSessions();
		$this->dropColumn('{{%lawsuit}}', 'room');
		$this->dropColumn('{{%lawsuit}}', 'due_at');
		$this->dropColumn('{{%lawsuit}}', 'presence_of_the_claimant');
		$this->dropColumn('{{%lawsuit}}', 'url');
		$this->dropColumn('{{%lawsuit}}', 'location');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown(): void {
		$this->addColumn('{{%lawsuit}}', 'room', $this->string()->null());
		$this->addColumn('{{%lawsuit}}', 'due_at', $this->dateTime()->null());
		$this->addColumn('{{%lawsuit}}', 'presence_of_the_claimant', $this->smallInteger()->null());
		$this->addColumn('{{%lawsuit}}', 'url', $this->string()->null());
		$this->addColumn('{{%lawsuit}}', 'location', $this->string()->null());

		$this->restoreFromSessions();
		$this->dropTable('{{%lawsuit_sessions}}');
	}

	private function insertSessions(): void {
		foreach ((new Query())
			->from(
				'{{%lawsuit}}'
			)
			->batch() as $rows) {

			$data = [];
			foreach ($rows as $row) {
				if (!empty($row['due_at'])) {
					$data[] = [
						'lawsuit_id' => $row['id'],
						'room' => $row['room'],
						'date_at' => $row['due_at'],
						'created_at' => $row['created_at'],
						'updated_at' => $row['updated_at'],
						'details' => $row['details'],
						'presence_of_the_claimant' => $row['presence_of_the_claimant'],
						'url' => $row['url'],
						'location' => $row['location'],
					];
				}
			}
			if (!empty($data)) {
				Yii::$app->db
					->createCommand()
					->batchInsert('{{%lawsuit_sessions}}', [
						'lawsuit_id',
						'room',
						'date_at',
						'created_at',
						'updated_at',
						'details',
						'presence_of_the_claimant',
						'url',
						'location',
					], $data)
					->execute();
			}
		}
	}

	private function restoreFromSessions(): void {
		foreach ((new Query())
			->from(
				'{{%lawsuit_sessions}}'
			)
			->batch() as $rows) {

			foreach ($rows as $row) {
				$insertColumns = [
					'room' => $row['room'],
					'due_at' => $row['date_at'],
					'presence_of_the_claimant' => $row['presence_of_the_claimant'],
					'url' => $row['url'],
					'location' => $row['location'],
					'id' => $row['lawsuit_id'],
				];
				$updateColumns = $insertColumns;
				unset($updateColumns['id']);
				$this->upsert('{{%lawsuit}}', $insertColumns, $updateColumns);
			}
		}
	}

}

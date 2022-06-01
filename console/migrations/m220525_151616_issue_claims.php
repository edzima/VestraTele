<?php

use common\models\issue\Issue;
use common\models\issue\IssueClaim;
use console\base\Migration;

/**
 * Class m220525_151616_issue_claims
 */
class m220525_151616_issue_claims extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable('{{%issue_claim}}', [
			'id' => $this->primaryKey(),
			'issue_id' => $this->integer()->notNull(),
			'type' => $this->string(10)->notNull(),
			'entity_responsible_id' => $this->integer()->notNull(),
			'date' => $this->date()->notNull(),
			'trying_value' => $this->decimal(10, 2),
			'obtained_value' => $this->decimal(10, 2),
			'percent_value' => $this->decimal(10, 2),
			'details' => $this->string()->null(),
		]);

		$this->addForeignKey('{{%fk_issue_claim_issue}}',
			'{{%issue_claim}}', 'issue_id',
			'{{%issue}}', 'id',
			'CASCADE', 'CASCADE'
		);

		$this->addForeignKey('{{%fk_issue_claim_entity_responsible}}',
			'{{%issue_claim}}', 'entity_responsible_id',
			'{{%issue_entity_responsible}}', 'id',
			'CASCADE', 'CASCADE'
		);

		$issueOldClaims = Issue::find()
			->select(['id', 'provision_type', 'provision_base', 'provision_value', 'entity_responsible_id', 'created_at'])
			->andWhere(['provision_type' => [1, 2]])
			->asArray()
			->all();

		$rows = [];
		foreach ($issueOldClaims as $data) {
			$rows[] = [
				'issue_id' => $data['id'],
				'type' => IssueClaim::TYPE_COMPANY,
				'percent_value' => $data['provision_type'] == 1 ? $data['provision_value'] : null,
				'trying_value' => $data['provision_base'],
				'details' => $data['provision_type'] == 1 ? null : $data['provision_value'],
				'entity_responsible_id' => $data['entity_responsible_id'],
				'date' => $data['created_at'],
			];
		}
		if (!empty($rows)) {
			$this->batchInsert('{{%issue_claim}}', [
				'issue_id',
				'type',
				'percent_value',
				'trying_value',
				'details',
				'entity_responsible_id',
				'date',
			], $rows);
		}

		$this->dropColumn('{{%issue}}', 'provision_value');
		$this->dropColumn('{{%issue}}', 'provision_base');
		$this->dropColumn('{{%issue}}', 'provision_type');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->addColumn('{{%issue}}', 'provision_value', $this->decimal(10, 2));
		$this->addColumn('{{%issue}}', 'provision_base', $this->decimal(10, 2));
		$this->addColumn('{{%issue}}', 'provision_type', $this->smallInteger()->notNull());

		$claims = IssueClaim::find()
			->select(['issue_id', 'trying_value', 'details', 'percent_value'])
			->andWhere(['type' => IssueClaim::TYPE_COMPANY])
			->asArray()
			->all();

		foreach ($claims as $data) {
			if (is_numeric($data['details']) || $data['details'] === null || is_numeric($data['percent_value'])) {
				$this->update('{{%issue}}', [
					'provision_type' => !empty($data['percent_value']) ? 1 : 2,
					'provision_value' => !empty($data['percent_value']) ? $data['percent_value'] : $data['details'],
					'provision_base' => $data['trying_value'],
				], ['id' => $data['issue_id']]);
			}
		}

		$this->dropTable('{{%issue_claim}}');
	}

}

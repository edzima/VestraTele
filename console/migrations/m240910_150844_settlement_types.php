<?php

use console\base\Migration;
use yii\db\Query;

/**
 * Class m240910_150844_settlement_types
 */
class m240910_150844_settlement_types extends Migration {

	public array $legacyNames = [];

	public function init(): void {
		parent::init();
		if (empty($this->legacyNames)) {
			$this->legacyNames = $this->defaultLegacyTypesNames();
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
//		$this->createTable('{{%settlement_type}}', [
//			'id' => $this->primaryKey(),
//			'name' => $this->string()->notNull()->unique(),
//			'is_active' => $this->boolean(),
//			'visibility_status' => $this->smallInteger(),
//			'options' => $this->json()->null(),
//		]);
//
//		$this->alterColumn('{{%issue_pay_calculation}}', 'type', $this->integer()->notNull());
		$this->createLegacyTypes();

		$this->addForeignKey(
			'{{%fk_issue_pay_calculation_type}}',
			'{{%issue_pay_calculation}}',
			'type',
			'{{%settlement_type}}',
			'id', 'CASCADE', 'CASCADE');

		$this->createTable('{{%settlement_type_issue_type}}', [
			'settlement_type_id' => $this->integer()->notNull(),
			'issue_type_id' => $this->integer()->notNull(),
		]);

		$this->addPrimaryKey('{{%PK_settlement_type_issue_types}}',
			'{{%settlement_type_issue_type}}', [
				'settlement_type_id',
				'issue_type_id',
			]);

		$this->addForeignKey(
			'{{%FK_settlement_type_issue_type}}',
			'{{%settlement_type_issue_type}}',
			'issue_type_id',
			'{{%issue_type}}',
			'id', 'CASCADE', 'CASCADE');

		$this->addForeignKey(
			'{{%FK_settlement_type_settlement_type}}',
			'{{%settlement_type_issue_type}}',
			'settlement_type_id',
			'{{%settlement_type}}',
			'id', 'CASCADE', 'CASCADE');

		$this->dropColumn('{{%issue_type}}', 'vat');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->addColumn('{{%issue_type}}', 'vat', $this->decimal());

		$this->dropTable('{{%settlement_type_issue_type}}');

		$this->dropForeignKey(
			'{{%fk_issue_pay_calculation_type}}',
			'{{%issue_pay_calculation}}'
		);
		$this->dropTable('{{%settlement_type}}');
	}

	protected function createLegacyTypes(): void {
		$types = (new Query())
			->select('type')
			->from('{{%issue_pay_calculation}}')
			->distinct()
			->column();

		$data = [];
		foreach ($types as $type) {
			$name = $this->legacyNames[$type] ?? $type;
			$data[] = [
				'id' => $type,
				'name' => $name,
				'is_active' => 1,
			];
		}
		if (!empty($data)) {
			$this->batchInsert('{{%settlement_type}}', [
				'id',
				'name',
				'is_active',
			], $data);
		}
	}

	protected function defaultLegacyTypesNames(): array {
		return [
			30 => Yii::t('settlement', 'Honorarium'),
			20 => Yii::t('settlement', 'Entry fee'),
			31 => Yii::t('settlement', 'Honorarium - Vindication'),
			10 => Yii::t('settlement', 'Administrative'),
			15 => Yii::t('settlement', 'Appeal'),
			40 => Yii::t('settlement', 'Lawyer'),
			41 => Yii::t('settlement', 'Appearance of Lawyer'),
			45 => Yii::t('settlement', 'Request for Justification'),
			50 => Yii::t('settlement', 'Subscription'),
			100 => Yii::t('settlement', 'Debt'),
			110 => Yii::t('settlement', 'Interest'),
			150 => Yii::t('settlement', 'Cost Refund: Company'),
			151 => Yii::t('settlement', 'Cost Refund: Legal represantion'),
			250 => Yii::t('settlement', 'VAT'),
			200 => Yii::t('settlement', 'Equity'),
		];
	}

}

<?php

use common\helpers\ArrayHelper;
use console\base\Migration;
use yii\db\Query;

/**
 * Class m241106_081715_cost_types
 */
class m241106_081715_cost_types extends Migration {

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
		$this->createTable('{{%cost_type}}', [
			'id' => $this->primaryKey(),
			'name' => $this->string()->notNull()->unique(),
			'is_active' => $this->boolean(),
			'is_for_settlement' => $this->boolean(),
			'options' => $this->json()->null(),
		]);

		if (!YII_ENV_TEST) {
			$this->createLegacyTypes();
			$this->ensureLegacyTypeId();
		}

		$this->renameColumn('{{%issue_cost}}', 'type', 'type_id');
		$this->alterColumn('{{%issue_cost}}', 'type_id', $this->integer()->notNull());

		$this->addForeignKey(
			'{{%fk_issue_cost_type}}',
			'{{%issue_cost}}',
			'type_id',
			'{{%cost_type}}',
			'id', 'CASCADE', 'CASCADE');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropForeignKey(
			'{{%fk_issue_cost_type}}',
			'{{%issue_cost}}'
		);
		$this->renameColumn('{{%issue_cost}}', 'type_id', 'type');
		$this->alterColumn('{{%issue_cost}}', 'type', $this->string()->notNull());
		$this->restoreLegacyType();
		$this->dropTable('{{%cost_type}}');
	}

	protected function createLegacyTypes(): void {
		$types = (new Query())
			->select('type')
			->from('{{%issue_cost}}')
			->distinct()
			->column();

		$data = [];
		foreach ($types as $type) {
			$name = $this->legacyNames[$type] ?? $type;
			$data[] = [
				'name' => $name,
				'is_active' => 1,
			];
		}
		if (!empty($data)) {
			$this->batchInsert('{{%cost_type}}', [
				'name',
				'is_active',
			], $data);
		}
	}

	protected function legacyNamesIds(): array {
		$names = ArrayHelper::map(
			(new Query())
				->select(['id', 'name'])
				->from('{{%cost_type}}')
				->all(),
			'name',
			'id');

		$ids = [];
		foreach ($this->legacyNames as $type => $name) {
			$id = $names[$name] ?? null;
			if ($id) {
				$ids[$id] = $type;
			}
		}
		return $ids;
	}

	private function ensureLegacyTypeId(): void {
		$typesIds = $this->legacyNamesIds();
		foreach ($typesIds as $id => $type) {
			$this->update('{{%issue_cost}}', [
				'type' => $id,
			], [
				'type' => $type,
			]);
		}
	}

	private function restoreLegacyType(): void {
		$typesIds = $this->legacyNamesIds();
		foreach ($typesIds as $id => $type) {
			$this->update('{{%issue_cost}}', [
				'type' => $type,
			], [
				'type' => $id,
			]);
		}
	}

	protected function defaultLegacyTypesNames(): array {
		return [
			'court_entry' => Yii::t('common', 'Court entry'),
			'appeal' => Yii::t('settlement', 'Appeal Cost'),
			'writ' => Yii::t('common', 'Writ'),
			'power_of_attorney' => Yii::t('common', 'Power of attorney'),
			'attestation' => Yii::t('settlement', 'Attestation'),
			'cession' => Yii::t('settlement', 'Cession'),
			'office' => Yii::t('common', 'Office'),
			'Appearance of a lawyer' => Yii::t('settlement', 'Appearance of Lawyer'),
			'court_expert' => Yii::t('settlement', 'Court expert'),
			'justification_of_the_judgment' => Yii::t('common', 'Justification of the judgment'),
			'installment' => Yii::t('common', 'Installment'),
			'shipments' => Yii::t('settlement', 'Shipments'),
			'commission_refund' => Yii::t('settlement', 'Commission Refund'),
			'pcc' => Yii::t('settlement', 'PCC'),
			'PIT-4' => Yii::t('settlement', 'PIT-4'),
			'purchase_of_receivables' => Yii::t('common', 'Purchase of receivables'),
		];
	}

}

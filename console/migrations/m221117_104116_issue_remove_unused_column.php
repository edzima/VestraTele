<?php

use console\base\Migration;
use yii\helpers\Console;

/**
 * Class m221117_104116_issue_remove_unused_column
 */
class m221117_104116_issue_remove_unused_column extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn('{{%issue}}', 'signature_act', $this->string(50));
		$this->dropIndex('signature_act', '{{%issue}}');
		$this->tryDropIssueColumn('client_first_name');
		$this->tryDropIssueColumn('client_surname');
		$this->tryDropIssueColumn('client_phone_1');
		$this->tryDropIssueColumn('client_phone_2');
		$this->tryDropIssueColumn('client_email');
		$this->tryDropIssueColumn('client_city_id');
		$this->tryDropIssueColumn('client_subprovince_id');
		$this->tryDropIssueColumn('client_city_code');
		$this->tryDropIssueColumn('client_street');

		$this->tryDropIssueColumn('victim_first_name');
		$this->tryDropIssueColumn('victim_surname');
		$this->tryDropIssueColumn('victim_phone');
		$this->tryDropIssueColumn('victim_email');
		$this->tryDropIssueColumn('victim_city_id');
		$this->tryDropIssueColumn('victim_subprovince_id');
		$this->tryDropIssueColumn('victim_city_code');
		$this->tryDropIssueColumn('victim_street');

		$this->tryDropIssueColumn('lawyer_id');
		$this->tryDropIssueColumn('agent_id');
		$this->tryDropIssueColumn('tele_id');
		$this->tryDropIssueColumn('payed');
	}

	private function tryDropIssueColumn(string $column): void {
		try {
			$this->dropColumn('{{%issue}}', $column);
		} catch (Exception $e) {
			Console::output($e->getMessage());
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->alterColumn('{{%issue}}', 'signature_act', $this->string(30)->unique());
	}

}

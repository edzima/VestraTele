<?php

use common\models\settlement\TransferType;
use yii\db\Migration;

/**
 * Class m210727_130632_pay_transfer_type_to_string
 */
class m210727_130632_pay_transfer_type_to_string extends Migration {

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->alterColumn('{{%issue_pay}}', 'transfer_type', $this->string());
		$this->update('{{%issue_pay}}', ['transfer_type' => TransferType::TRANSFER_TYPE_CASH], ['transfer_type' => 1]);
		$this->update('{{%issue_pay}}', ['transfer_type' => TransferType::TRANSFER_TYPE_BANK], ['transfer_type' => 2]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->alterColumn('{{%issue_pay}}', 'transfer_type', $this->smallInteger());
	}

}

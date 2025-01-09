<?php

use common\modules\court\modules\spi\models\AppealInterface;
use console\base\Migration;

/**
 * Class m241021_161511_lawsuit_rul
 */
class m250109_101620_court_spi_data extends Migration {

	public array $courtAppeal = [
		AppealInterface::APPEAL_BIALYSTOK => 'Białymstoku',
		AppealInterface::APPEAL_GDANSK => 'Gdańsku',
		AppealInterface::APPEAL_KATOWICE => 'Katowicach',
		AppealInterface::APPEAL_KRAKOW => 'Krakowie',
		AppealInterface::APPEAL_LUBLIN => 'Lublinie',
		AppealInterface::APPEAL_LODZ => 'Łodzi',
		AppealInterface::APPEAL_POZNAN => 'Poznaniu',
		AppealInterface::APPEAL_RZESZOW => 'Rzeszowie',
		AppealInterface::APPEAL_SZCZECIN => 'Szczecinie',
		AppealInterface::APPEAL_WARSZAWA => 'Warszawie',
		AppealInterface::APPEAL_WROCLAW => 'Wrocławiu',
	];

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn('{{%court}}', 'spi_appeal', $this->string(30));
		$this->addColumn('{{%court}}', 'spi_data', $this->json());
		$this->updateAppeal();
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropColumn('{{%court}}', 'spi_appeal');
		$this->dropColumn('{{%court}}', 'spi_data');
	}

	protected function updateAppeal(): void {
		foreach ($this->courtAppeal as $appeal => $name) {
			$this->update('{{%court}}', [
				'spi_appeal' => $appeal,
			],
				['AND', ['type' => 'SA'], ['like', 'name', $name]],
			);
		}
	}

}

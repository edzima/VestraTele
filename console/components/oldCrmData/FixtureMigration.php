<?php

namespace console\components\oldCrmData;

use common\models\entityResponsible\EntityResponsible;
use Yii;
use yii\db\Migration;
use yii\db\Query;

class FixtureMigration extends Migration {

	public $db = 'oldDb';

	private const TABLE_ISSUE_NAME = '{{%issues}}';
	public const COLUMN_ENTITY = 'issue_insurer';
	private const COLUMN_TYPE = 'issue_type';
	private const COLUMN_CLIENT_REGION = 'client_region';
	private const COLUMN_VICTIM_REGION = 'victim_region';
	private const ENTITY_FIXTURE = [
		'Nieznany' => ['', '0', '?', 'A 77', 'PROJEKT B'],
		'Alianz' => ['ALLIANZ', 'ALLIANZ POLSKA S.A.'],
		'AVIVA' => ['AVIVA TU S.A.'],
		'AXA Direct' => ['AXA Direct ', 'AXA UBEZPIECZENIA'],
		'Balcia' => ['Balcia Insurance', 'Balcia SE'],
		'Benefia' => ['BENEFIA', 'BENEFIA Ubezpieczenia'],
		'Concordia' => ['Concordia', 'CONCORDIA'],
		'Compensa' => ['Compensa TU S.A. ', 'Compensa ', 'COMPENSA'],
		'ERGO Hestia' => ['ERGO HESTIA', 'ERGO HESTIA S.A.', 'ERGO HESTIA S.A. ???'],
		'Generali' => ['Generali ', 'GENERALI TU S.A. '],
		'Gothear' => ['GOTHAER ', 'Gothaer TU S.A.'],
		'InterRisk' => ['InterRisk TU S.A. '],
		'Liberty' => ['LIBERTY UBEZPIECZENIA'],
		'Proama' => ['PROAMA S.A.', 'Proama '],
		'PZU' => ['PZU S.A.', 'PZU Życie S.A. '],
		'TUW' => ['TUW ', 'TUW "TUW"', 'TUW TUW'],
		'TUZ' => ['TUW TUZ', 'TUZ UBEZPIECZENIA'],
		'UNIQA' => ['Uniqa'],
		'Warta' => ['WARTA ', 'WARTA S.A.'],
	];

	private const TYPE_FIXTURE = [
		'Komunikacyjny' => ['Komunkacyjny', 'Komunikatycyjny'],
		'Świadczenie pielęgnacyjne' => [' Świadczenie pielęgnacyjne', 'Świadczenie pielęgnacykne', 'Świadczenie pielęgnacyje', 'Świadczenie pielęgnaccyjne'],
	];

	private const REGION_FIXTURE = [
		'DOLNOŚLĄSKIE' => ['Dolnośląskie'],
		'KUJAWSKO-POMORSKIE' => ['kujawsko - pomorskie', 'Kujawsko-Pomorskie', 'kijawsko - pomorskie'],
		'LUBELSKIE' => ['Lubelskie'],
		'LUBUSKIE' => ['lubuskie'],
		'MAZOWIECKIE' => ['mazowieckie'],
		'MAŁOPOLSKIE' => ['małopolskie'],
		'OPOLSKIE' => ['opolskie'],
		'PODKARPACKIE' => ['Podkarpackie'],
		'PODLASKIE' => ['podlaskie'],
		'POMORSKIE' => ['Pomorskie', 'pomoskie'],
		'ŚLĄSKIE' => ['śląskie'],
		'ŚWIĘTOKRZYSKIE' => ['Świętokrzyskie', 'sandomierski'],
		'WARMIŃSKO-MAZURSKIE' => ['warmińska-mazurskie', 'warmińsko - mazurskie', 'warmińsko-mazurskie'],
		'WIELKOPOLSKIE' => ['wielkopolrskie', 'wielkopolski', 'Wielkopolskie', 'wielkospolskie'],
		'ZACHODNIOPOMORSKIE' => ['Zachodnio-Pomorskie', 'Zachodniopomorskie', 'zachodnipomorskie', 'zachodnopomorskie'],
		'ŁÓDZKIE' => ['łódzki', 'łódzkie'],
		'Nieznany' => '0',
	];

	public function fixEntity(): void {
		$this->fixColumn(static::TABLE_ISSUE_NAME, static::COLUMN_ENTITY, static::ENTITY_FIXTURE);
		Yii::$app->db->createCommand()
			->batchInsert(
				EntityResponsible::tableName(),
				['name'],
				$this->getEntityNames())
			->execute();
	}

	public function fixType(): void {
		$this->fixColumn(static::TABLE_ISSUE_NAME, static::COLUMN_TYPE, static::TYPE_FIXTURE);
	}

	public function fixRegion(): void {
		$this->fixColumn(static::TABLE_ISSUE_NAME, static::COLUMN_CLIENT_REGION, static::REGION_FIXTURE);
		$this->fixColumn(static::TABLE_ISSUE_NAME, static::COLUMN_VICTIM_REGION, static::REGION_FIXTURE);
	}

	private function getEntityNames(): array {
		return (new Query())->select(static::COLUMN_ENTITY)
			->distinct()
			->from(static::TABLE_ISSUE_NAME)
			->orderBy(static::COLUMN_ENTITY)
			->all($this->db);
	}

	private function fixColumn(string $table, string $column, array $fixture): void {
		foreach ($fixture as $name => $values) {
			$this->update(
				$table,
				[$column => $name],
				[$column => $values]
			);
		}
	}

}

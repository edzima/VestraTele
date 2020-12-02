<?php

namespace backend\tests\fixtures;

use common\fixtures\issue\TypeFixture;
use common\fixtures\provision\ProvisionTypeFixture;

class ProvisionFixtureHelper {

	public static function typesFixtures(): array {
		return [
			'issue-type' => [
				'class' => TypeFixture::class,
				'dataFile' => codecept_data_dir() . 'issue/type.php',
			],
			'provision-type' => [
				'class' => ProvisionTypeFixture::class,
				'dataFile' => codecept_data_dir() . 'provision/type.php',
			],
		];
	}
}

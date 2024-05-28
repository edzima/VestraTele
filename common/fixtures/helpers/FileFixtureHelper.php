<?php

namespace common\fixtures\helpers;

use common\fixtures\file\FileAccessFixture;
use common\fixtures\file\FileFixture;
use common\fixtures\file\FileTypeFixture;
use Yii;

class FileFixtureHelper extends BaseFixtureHelper {

	protected static function getDefaultDataDirPath(): string {
		return Yii::getAlias('@common/tests/_data/file/');
	}

	public static function fixtures(): array {
		return array_merge(
		//	static::access(),
			static::file(),
			static::type()
		);
	}

	public static function access(): array {
		return [
			'access' => [
				'class' => FileAccessFixture::class,
				'dataFile' => static::getDataDirPath() . 'file-access.php',
			],
		];
	}

	public static function file(): array {
		return [
			'file' => [
				'class' => FileFixture::class,
				'dataFile' => static::getDataDirPath() . 'file.php',
			],
		];
	}

	public static function type(): array {
		return [
			'type' => [
				'class' => FileTypeFixture::class,
				'dataFile' => static::getDataDirPath() . 'file-type.php',
			],
		];
	}

}

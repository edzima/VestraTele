<?php

namespace common\fixtures\helpers;

use common\fixtures\lead\LeadFixture;
use common\fixtures\lead\LeadReportFixture;
use common\fixtures\lead\LeadReportSchemaFixture;
use common\fixtures\lead\LeadReportSchemaStatusTypeFixture;
use common\fixtures\lead\LeadStatusFixture;
use common\fixtures\lead\LeadTypeFixture;
use common\fixtures\lead\LeadUserFixture;
use Yii;

class LeadFixtureHelper {

	public const LEAD = 'lead';
	public const TYPE = 'type';
	public const STATUS = 'status';

	public const REPORT = 'report';
	public const REPORT_SCHEMA = 'report-schema';
	public const USER = 'user';

	public static function dataDir(): string {
		return Yii::getAlias('@common/tests/_data/lead/');
	}

	public static function reports(): array {
		return [
			static::REPORT_SCHEMA => [
				'class' => LeadReportSchemaFixture::class,
				'dataFile' => static::dataDir() . 'report-schema.php',
			],
			'report-schema-status-type' => [
				'class' => LeadReportSchemaStatusTypeFixture::class,
				'dataFile' => static::dataDir() . 'report-schema-status-type.php',
			],
			static::REPORT => [
				'class' => LeadReportFixture::class,
				'dataFile' => static::dataDir() . 'report.php',
			],

		];
	}

	public static function leads(): array {
		return [
			static::USER => [
				'class' => LeadUserFixture::class,
				'dataFile' => static::dataDir() . 'user.php',
			],
			static::LEAD => [
				'class' => LeadFixture::class,
				'dataFile' => static::dataDir() . 'lead.php',
			],
			static::TYPE => [
				'class' => LeadTypeFixture::class,
				'dataFile' => static::dataDir() . 'type.php',
			],
			static::STATUS => [
				'class' => LeadStatusFixture::class,
				'dataFile' => static::dataDir() . 'status.php',
			],
		];
	}
}

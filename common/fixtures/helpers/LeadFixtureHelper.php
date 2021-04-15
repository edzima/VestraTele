<?php

namespace common\fixtures\helpers;

use common\modules\lead\fixtures\CampaignFixture;
use common\modules\lead\fixtures\LeadFixture;
use common\modules\lead\fixtures\LeadReportFixture;
use common\modules\lead\fixtures\LeadReportSchemaFixture;
use common\modules\lead\fixtures\LeadReportSchemaStatusTypeFixture;
use common\modules\lead\fixtures\SourceFixture;
use common\modules\lead\fixtures\StatusFixture;
use common\modules\lead\fixtures\TypeFixture;
use common\modules\lead\fixtures\UserFixture;
use Yii;

class LeadFixtureHelper {

	public const LEAD = 'lead';
	public const TYPE = 'type';
	private const STATUS = 'status';
	private const SOURCE = 'source';

	private const REPORT = 'report';
	private const REPORT_SCHEMA = 'report-schema';
	private const USER = 'user';

	public static function dataDir(): string {
		return Yii::getAlias('@common/tests/_data/lead/');
	}

	public static function leads(): array {
		return [
			static::USER => [
				'class' => UserFixture::class,
				'dataFile' => static::dataDir() . 'user.php',
			],
			static::LEAD => [
				'class' => LeadFixture::class,
				'dataFile' => static::dataDir() . 'lead.php',
			],
			static::TYPE => [
				'class' => TypeFixture::class,
				'dataFile' => static::dataDir() . 'type.php',
			],
			static::STATUS => [
				'class' => StatusFixture::class,
				'dataFile' => static::dataDir() . 'status.php',
			],
			static::SOURCE => [
				'class' => SourceFixture::class,
				'dataFile' => static::dataDir() . 'source.php',
			],
		];
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

	public static function campaign(): array {
		return [
			static::USER => [
				'class' => UserFixture::class,
				'dataFile' => static::dataDir() . 'user.php',
			],
			static::SOURCE => [
				'class' => CampaignFixture::class,
				'dataFile' => static::dataDir() . 'campaign.php',
			],
		];
	}

	public static function source(): array {
		return [
			static::USER => [
				'class' => UserFixture::class,
				'dataFile' => static::dataDir() . 'user.php',
			],
			static::SOURCE => [
				'class' => SourceFixture::class,
				'dataFile' => static::dataDir() . 'source.php',
			],
			static::TYPE => [
				'class' => TypeFixture::class,
				'dataFile' => static::dataDir() . 'type.php',
			],
		];
	}

}

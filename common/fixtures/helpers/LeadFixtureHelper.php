<?php

namespace common\fixtures\helpers;

use common\fixtures\ReminderFixture;
use common\modules\lead\fixtures\CampaignFixture;
use common\modules\lead\fixtures\LeadAnswerFixture;
use common\modules\lead\fixtures\LeadFixture;
use common\modules\lead\fixtures\LeadReportFixture;
use common\modules\lead\fixtures\LeadQuestionFixture;
use common\modules\lead\fixtures\ReminderFixture as LeadReminderFixture;
use common\modules\lead\fixtures\SourceFixture;
use common\modules\lead\fixtures\StatusFixture;
use common\modules\lead\fixtures\TypeFixture;
use common\modules\lead\fixtures\UserFixture;
use common\modules\lead\models\LeadUser;
use Yii;
use yii\test\ActiveFixture;

class LeadFixtureHelper {

	public const LEAD = 'lead.lead';
	public const TYPE = 'lead.type';
	private const STATUS = 'lead.status';
	private const SOURCE = 'lead.source';

	private const REPORT = 'lead.report';
	private const QUESTION = 'lead.question';
	private const USER = 'lead.user';
	private const LEAD_USER = 'lead.lead-user';
	private const REMINDER = 'lead.reminder';
	private const LEAD_REMINDER = 'lead.lead-reminder';
	private const ANSWER = 'lead.answer';

	public static function dataDir(): string {
		return Yii::getAlias('@common/tests/_data/lead/');
	}

	public static function lead(): array {
		return [
			static::LEAD => [
				'class' => LeadFixture::class,
				'dataFile' => static::dataDir() . 'lead.php',
			],
		];
	}

	public static function leads(): array {
		return array_merge(
			static::lead(),
			static::source(),
			static::status(),
			static::user(),
		);
	}

	public static function status(): array {
		return [
			static::STATUS => [
				'class' => StatusFixture::class,
				'dataFile' => static::dataDir() . 'status.php',
			],
		];
	}


	public static function type(): array {
		return [
			static::TYPE => [
				'class' => TypeFixture::class,
				'dataFile' => static::dataDir() . 'type.php',
			],
		];
	}

	public static function reports(): array {
		return [
			static::ANSWER => [
				'class' => LeadAnswerFixture::class,
				'dataFile' => static::dataDir() . 'answer.php',
			],
			static::QUESTION => [
				'class' => LeadQuestionFixture::class,
				'dataFile' => static::dataDir() . 'question.php',
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
			static::SOURCE => [
				'class' => SourceFixture::class,
				'dataFile' => static::dataDir() . 'source.php',
			],
			static::TYPE => [
				'class' => TypeFixture::class,
				'dataFile' => static::dataDir() . 'type.php',
			],
			static::USER => [
				'class' => UserFixture::class,
				'dataFile' => static::dataDir() . 'user.php',
			],
		];
	}

	public static function question(): array {
		return [
			static::QUESTION => [
				'class' => LeadQuestionFixture::class,
				'dataFile' => static::dataDir() . 'question.php',
			],
			static::STATUS => [
				'class' => StatusFixture::class,
				'dataFile' => static::dataDir() . 'status.php',
			],
			static::TYPE => [
				'class' => TypeFixture::class,
				'dataFile' => static::dataDir() . 'type.php',
			],
		];
	}

	public static function user(): array {
		return [
			static::USER => [
				'class' => UserFixture::class,
				'dataFile' => static::dataDir() . 'user.php',
			],
			static::LEAD_USER => [
				'class' => ActiveFixture::class,
				'modelClass' => LeadUser::class,
				'dataFile' => static::dataDir() . 'lead-user.php',
			],
		];
	}

	public static function reminder(): array {
		return [
			static::REMINDER => [
				'class' => ReminderFixture::class,
				'dataFile' => static::dataDir() . 'reminder.php',
			],
			static::LEAD_REMINDER => [
				'class' => LeadReminderFixture::class,
				'dataFile' => static::dataDir() . 'lead-reminder.php',
			],
		];
	}

}

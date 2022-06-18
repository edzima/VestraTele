<?php

namespace common\fixtures\helpers;

use common\fixtures\ReminderFixture;
use common\modules\lead\fixtures\CampaignFixture;
use common\modules\lead\fixtures\DialerFixture;
use common\modules\lead\fixtures\DialerTypeFixture;
use common\modules\lead\fixtures\LeadAnswerFixture;
use common\modules\lead\fixtures\LeadFixture;
use common\modules\lead\fixtures\LeadQuestionFixture;
use common\modules\lead\fixtures\LeadReportFixture;
use common\modules\lead\fixtures\MarketFixture;
use common\modules\lead\fixtures\MarketUserFixture;
use common\modules\lead\fixtures\ReminderFixture as LeadReminderFixture;
use common\modules\lead\fixtures\SourceFixture;
use common\modules\lead\fixtures\StatusFixture;
use common\modules\lead\fixtures\TypeFixture;
use common\modules\lead\fixtures\UserFixture;
use common\modules\lead\models\ActiveLead;
use common\modules\lead\models\Lead;
use common\modules\lead\models\LeadReport;
use common\modules\lead\models\LeadUser;
use Yii;
use yii\helpers\Json;
use yii\test\ActiveFixture;

class LeadFixtureHelper extends BaseFixtureHelper {

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
	private const DIALER = 'lead.dialer';
	private const DIALER_TYPE = 'lead.dialer-type';
	private const MARKET = 'lead.market';
	private const MARKET_USER = 'lead.market-user';

	public const DEFAULT_PHONE = '+48 123-123-123';
	public const DEFAULT_SOURCE_ID = 1;

	public static function market(): array {
		return [
			static::MARKET => [
				'class' => MarketFixture::class,
				'dataFile' => static::getDataDirPath() . 'market.php',
			],
			static::MARKET_USER => [
				'class' => MarketUserFixture::class,
				'dataFile' => static::getDefaultDataDirPath() . 'market-user.php',
			],
		];
	}

	public function haveLead(array $attributes): int {
		if (!isset($attributes['phone'])) {
			$attributes['phone'] = static::DEFAULT_PHONE;
		}
		if (!isset($attributes['source_id'])) {
			$attributes['source_id'] = static::DEFAULT_SOURCE_ID;
		}

		if (!isset($attributes['data'])) {
			$attributes['data'] = Json::encode($attributes);
		}
		return $this->tester->haveRecord(Lead::class, $attributes);
	}

	public function grabLeadById(int $id): ActiveLead {
		return $this->grabLead(['id' => $id]);
	}

	public function grabLead(array $attributes): ActiveLead {
		return $this->tester->grabRecord(Lead::class, $attributes);
	}

	public static function getDefaultDataDirPath(): string {
		return Yii::getAlias('@common/tests/_data/lead/');
	}

	public static function lead(): array {
		return [
			static::LEAD => [
				'class' => LeadFixture::class,
				'dataFile' => static::getDataDirPath() . 'lead.php',
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
				'dataFile' => static::getDataDirPath() . 'status.php',
			],
		];
	}

	public static function reports(): array {
		return [
			static::ANSWER => [
				'class' => LeadAnswerFixture::class,
				'dataFile' => static::getDataDirPath() . 'answer.php',
			],
			static::QUESTION => [
				'class' => LeadQuestionFixture::class,
				'dataFile' => static::getDataDirPath() . 'question.php',
			],
			static::REPORT => [
				'class' => LeadReportFixture::class,
				'dataFile' => static::getDataDirPath() . 'report.php',
			],

		];
	}

	public static function campaign(): array {
		return [
			static::USER => [
				'class' => UserFixture::class,
				'dataFile' => static::getDataDirPath() . 'user.php',
			],
			static::SOURCE => [
				'class' => CampaignFixture::class,
				'dataFile' => static::getDataDirPath() . 'campaign.php',
			],
		];
	}

	public static function source(): array {
		return [
			static::SOURCE => [
				'class' => SourceFixture::class,
				'dataFile' => static::getDataDirPath() . 'source.php',
			],
			static::TYPE => [
				'class' => TypeFixture::class,
				'dataFile' => static::getDataDirPath() . 'type.php',
			],
			static::USER => [
				'class' => UserFixture::class,
				'dataFile' => static::getDataDirPath() . 'user.php',
			],
		];
	}

	public static function question(): array {
		return [
			static::QUESTION => [
				'class' => LeadQuestionFixture::class,
				'dataFile' => static::getDataDirPath() . 'question.php',
			],
			static::STATUS => [
				'class' => StatusFixture::class,
				'dataFile' => static::getDataDirPath() . 'status.php',
			],
			static::TYPE => [
				'class' => TypeFixture::class,
				'dataFile' => static::getDataDirPath() . 'type.php',
			],
		];
	}

	public static function user(): array {
		return [
			static::USER => [
				'class' => UserFixture::class,
				'dataFile' => static::getDataDirPath() . 'user.php',
			],
			static::LEAD_USER => [
				'class' => ActiveFixture::class,
				'modelClass' => LeadUser::class,
				'dataFile' => static::getDataDirPath() . 'lead-user.php',
			],
		];
	}

	public static function reminder(): array {
		return [
			static::REMINDER => [
				'class' => ReminderFixture::class,
				'dataFile' => static::getDataDirPath() . 'reminder.php',
			],
			static::LEAD_REMINDER => [
				'class' => LeadReminderFixture::class,
				'dataFile' => static::getDataDirPath() . 'lead-reminder.php',
			],
		];
	}

	public static function dialer(): array {
		return [
			static::DIALER => [
				'class' => DialerFixture::class,
				'dataFile' => static::getDataDirPath() . 'dialer.php',
			],
			static::DIALER_TYPE => [
				'class' => DialerTypeFixture::class,
				'dataFile' => static::getDataDirPath() . 'dialer-type.php',
			],
		];
	}

	public function seeLead(array $atributtes) {
		$this->tester->seeRecord(Lead::class, $atributtes);
	}

	public function dontSeeLead(array $atributtes) {
		$this->tester->dontSeeRecord(Lead::class, $atributtes);
	}

	public function seeReport(array $atributtes) {
		$this->tester->seeRecord(LeadReport::class, $atributtes);
	}

	public function dontSeeReport(array $atributtes) {
		$this->tester->dontSeeRecord(LeadReport::class, $atributtes);
	}

}

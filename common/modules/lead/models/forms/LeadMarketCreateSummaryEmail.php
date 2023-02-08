<?php

namespace common\modules\lead\models\forms;

use common\models\user\query\UserQuery;
use common\models\user\User;
use common\modules\lead\models\LeadMarket;
use common\modules\lead\models\LeadMarketUser;
use Yii;
use yii\base\Model;

class LeadMarketCreateSummaryEmail extends Model {

	public const SCENARIO_TODAY = 'today';
	public const SCENARIO_YESTERDAY = 'yesterday';

	public string $createdFrom = '';
	public string $createdTo = '';

	public array $emails = [];

	public function rules(): array {
		return [
			[
				['createdFrom'], 'default', 'value' => function (): string {
				return date('Y-m-d 00:00:00');
			}, 'on' => static::SCENARIO_TODAY,
			],
			[
				['createdTo'], 'default', 'value' => function (): string {
				return date('Y-m-d 23:59:59');
			}, 'on' => static::SCENARIO_TODAY,
			],
			[
				['createdFrom'], 'default', 'value' => function (): string {
				return date('Y-m-d 00:00:00', strtotime('- 1 day'));
			}, 'on' => static::SCENARIO_YESTERDAY,
			],
			[
				['createdTo'], 'default', 'value' => function (): string {
				return date('Y-m-d 23:59:59', strtotime('- 1 day'));
			}, 'on' => static::SCENARIO_YESTERDAY,
			],
			[['createdFrom', 'createdTo'], 'required'],
			[['createdFrom', 'createdTo'], 'datetime', 'format' => 'php:Y-m-d H:i:s'],
			[
				'emails', 'default', 'value' => $this->getMarketUsersEmails(), 'when' => function (): bool {
				return empty($this->emails);
			},
			],
			['emails', 'required'],
		];
	}

	public function sendEmail(): ?int {
		if (!$this->validate()) {
			return null;
		}
		$models = $this->findModels();
		if (empty($models)) {
			Yii::warning('Not find models', __METHOD__);
			return null;
		}
		$grouped = $this->groupModelsByAddressRegion($models);
		$count = count($models);
		if (!Yii::$app
			->mailer
			->compose(
				['html' => 'leadMarketCreateSummary-html', 'text' => 'leadMarketCreateSummary-text'],
				[
					'regionsModels' => $grouped['regions'],
					'withoutRegionsModels' => $grouped['without'],
					'totalCount' => $count,
				]
			)
			->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->name . ' robot'])
			->setTo($this->emails)
			->setSubject(Yii::t('lead', 'New {count} Leads on Market.', [
				'count' => $count,
			]))
			->send()) {
			return null;
		}
		return $count;
	}

	public function findModels(): array {
		return LeadMarket::find()
			->andWhere(['>=', LeadMarket::tableName() . '.created_at', $this->createdFrom])
			->andWhere(['<=', LeadMarket::tableName() . '.created_at', $this->createdTo])
			->with('lead.addresses.address.city')
			->all();
	}

	/**
	 * @param LeadMarket[] $models
	 * @return LeadMarket[][]
	 */
	public function groupModelsByAddressRegion(array $models): array {
		$regions = [];
		$withoutRegions = [];
		foreach ($models as $model) {
			$address = $model->lead->getCustomerAddress();
			if ($address !== null && $address->city !== null) {
				$region = $address->city->region_id;
				if (!isset($regions[$region])) {
					$regions[$region] = [];
				}
				$regions[$region][] = $model;
			} else {
				$withoutRegions[] = $model;
			}
		}
		ksort($regions);
		return [
			'regions' => $regions,
			'without' => $withoutRegions,
		];
	}

	public function getMarketUsersEmails(): array {
		return LeadMarketUser::find()
			->select([
				User::tableName() . '.email',
				User::tableName() . '.status',
			])
			->joinWith([
				'user' => function (UserQuery $query) {
					$query->active();
				},
			])
			->column();
	}

}

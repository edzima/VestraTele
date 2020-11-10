<?php

namespace common\modules\issue\widgets;

use Closure;
use common\models\issue\IssueUser;
use common\widgets\FieldsetDetailView;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

/**
 *
 * @property-read array $users
 */
class IssueUsersWidget extends IssueWidget {

	public const TYPE_WORKERS = 'workers';
	public const TYPE_CUSTOMERS = 'customers';

	public string $type;

	public array $fieldsetOptions = [
		'toggle' => false,
		'htmlOptions' => [
			'class' => 'col-md-4',
		],
		'detailConfig' => [
			'attributes' => [
				'email:email',
				'profile.phone',
			],
		],
	];

	public ?Closure $legend = null;
	public ?Closure $withAddress = null;
	public bool $legendEncode = true;

	public function run(): string {
		$users = $this->getUsers();
		if (empty($users)) {
			return '';
		}
		return $this->render('issue-users', [
			'users' => $users,
			'widget' => $this,
		]);
	}

	public function getUsers(): array {
		switch ($this->type) {
			case static::TYPE_CUSTOMERS:
				return $this->model->getUsers()->withTypes(IssueUser::TYPES_CUSTOMERS)->all();
			case static::TYPE_WORKERS:
				return $this->model->getUsers()->withTypes(IssueUser::TYPES_WORKERS)->all();
			default:
				throw new InvalidConfigException('Invalid $type.');
		}
	}

	public function renderUser(IssueUser $issueUser): string {
		$options = $this->fieldsetOptions;
		$class = ArrayHelper::remove($options, 'class', FieldsetDetailView::class);
		if ($this->withAddress !== null) {
			if (call_user_func($this->withAddress, $issueUser) && $issueUser->user->homeAddress) {
				$address = $issueUser->user->homeAddress;
				$name = $address->city->nameWithRegionAndDistrict;
				if ($address->postal_code) {
					$name = $address->postal_code . ' ' . $name;
				}
				$options['detailConfig']['attributes'] = array_merge($options['detailConfig']['attributes'], [
					[
						'attribute' => 'homeAddress.city.nameWithRegionAndDistrict',
						'label' => Yii::t('common', 'City'),
						'value' => $name,
					],
					[
						'attribute' => 'homeAddress.info',
						'label' => 'Ulica i nr',
						'visible' => !empty($address->info),
					],
				]);
			}
		}
		$options['legend'] = $this->generateLegend($issueUser);
		$options['legendOptions']['encode'] = $this->legendEncode;
		$options['detailConfig']['model'] = $issueUser->user;
		return $class::widget($options);
	}

	public function generateLegend(IssueUser $issueUser): string {
		if ($this->legend instanceof Closure) {
			$legend = $this->legend;
			return $legend($issueUser);
		}
		return $issueUser->getTypeName();
	}
}

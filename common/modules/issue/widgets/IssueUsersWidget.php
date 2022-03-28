<?php

namespace common\modules\issue\widgets;

use backend\helpers\Html;
use Closure;
use common\models\issue\Issue;
use common\models\issue\IssueUser;
use common\models\user\User;
use common\widgets\FieldsetDetailView;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

/**
 *
 * @property-read array $users
 */
class IssueUsersWidget extends Widget {

	public const TYPE_WORKERS = 'workers';
	public const TYPE_CUSTOMERS = 'customers';

	public Issue $model;

	public string $type;

	public array $fieldsetOptions = [];
	public bool $withCheckEmailVisibility = true;

	public array $containerOptions = [
		'class' => 'issue-users',
	];

	public ?Closure $legend = null;
	public ?Closure $afterLegend = null;
	public ?Closure $withAddress = null;
	public bool $legendEncode = true;
	public bool $withTraits = false;

	public function getDefaultFieldsetOptions(IssueUser $issueUser): array {
		$user = $issueUser->user;
		$traits = [];
		if ($this->withTraits) {
			$traits = $issueUser->user->getTraits()
				->joinWith('trait')
				->andWhere(['show_on_issue_view' => true])
				->all();
		}

		return [
			'toggle' => false,
			'htmlOptions' => [
				//		'class' => 'col-md-6',
			],
			'detailConfig' => [
				'attributes' => [
					[
						'attribute' => 'email',
						'format' => 'email',
						'visible' => $this->isEmailVisible($user),
					],
					[
						'attribute' => 'profile.phone',
						'format' => 'tel',
						'label' => Yii::t('common', 'Phone number'),
						'visible' => !empty($user->profile->phone),
					],
					[
						'attribute' => 'profile.phone_2',
						'label' => Yii::t('common', 'Phone number 2'),
						'format' => 'tel',
						'visible' => !empty($user->profile->phone_2),
					],
					[
						'label' => Yii::t('common', 'Traits'),
						'visible' => !empty($traits),
						'value' => implode(', ', ArrayHelper::getColumn($traits, 'name')),
					],
				],
			],
		];
	}

	public function isEmailVisible(User $user): bool {
		if (empty($user->email)) {
			return false;
		}
		return !$this->withCheckEmailVisibility || !$user->profile->email_hidden_in_frontend_issue;
	}

	/**
	 * @return string
	 * @throws InvalidConfigException
	 */
	public function run(): string {
		$users = $this->getUsers();
		if (empty($users)) {
			return '';
		}
		$content = [];
		$content[] = Html::beginTag('div', $this->containerOptions);
		foreach ($users as $user) {
			$content[] = $this->renderUser($user);
		}
		$content[] = Html::endTag('div');
		return implode("\n", $content);
	}

	/**
	 * @return array
	 * @throws InvalidConfigException when type is not correct.
	 */
	public function getUsers(): array {
		switch ($this->type) {
			case static::TYPE_CUSTOMERS:
				return $this->sort($this->model
					->getUsers()
					->with('user.userProfile')
					->withTypes(IssueUser::TYPES_CUSTOMERS)
					->indexBy('type')
					->all(), IssueUser::TYPES_CUSTOMERS);
			case static::TYPE_WORKERS:
				return $this->sort($this->model
					->getUsers()
					->with('user.userProfile')
					->withTypes(IssueUser::TYPES_WORKERS)
					->indexBy('type')
					->all(), IssueUser::TYPES_WORKERS);
			default:
				throw new InvalidConfigException('Invalid $type.');
		}
	}

	protected function sort(array $users, array $types): array {
		$sorted = [];
		foreach ($types as $type) {
			if (isset($users[$type])) {
				$sorted[$type] = $users[$type];
			}
		}
		return $sorted;
	}

	public function renderUser(IssueUser $issueUser): string {
		$options = $this->fieldsetOptions;
		if (empty($options)) {
			$options = $this->getDefaultFieldsetOptions($issueUser);
		}
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
		$options['afterLegend'] = $this->renderAfterLegend($issueUser);
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

	public function renderAfterLegend(IssueUser $issueUser): string {
		if ($this->afterLegend instanceof Closure) {
			return call_user_func($this->afterLegend, $issueUser);
		}
		return '';
	}
}

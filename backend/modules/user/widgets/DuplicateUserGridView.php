<?php

namespace backend\modules\user\widgets;

use backend\helpers\Html;
use backend\helpers\Url;
use backend\widgets\GridView;
use common\models\user\User;
use common\models\user\Worker;
use common\widgets\grid\ActionColumn;
use Yii;

class DuplicateUserGridView extends GridView {

	public string $actionTemplate = '{create-issue} {link} {view} {update}';

	public function init() {
		if (empty($this->columns)) {
			$this->columns = $this->defaultColumns();
		}
		if (empty($this->panel)) {
			$this->panel = [
				'type' => GridView::TYPE_WARNING,
				'heading' => '<i class="fa fa-users"></i> ' . Yii::t('backend', 'Duplicates'),
				'after' => false,
				'footer' => false,
				'before' => false,
			];
		}
		parent::init();
	}

	public function defaultColumns(): array {
		return [
			[
				'attribute' => 'firstname',
				'value' => 'profile.firstname',
				'label' => Yii::t('common', 'Firstname'),
			],
			[
				'attribute' => 'lastname',
				'value' => 'profile.lastname',
				'label' => Yii::t('common', 'Lastname'),
			],
			[
				'attribute' => 'city',
				'value' => function (User $user): ?string {
					if ($user->homeAddress) {
						return $user->homeAddress->getCityWithPostalCode(true);
					}
					return null;
				},
				'format' => 'html',
				'label' => Yii::t('address', 'City'),
			],
			[
				'attribute' => 'addressInfo',
				'value' => 'homeAddress.info',
				'label' => Yii::t('address', 'Info'),
			],
			'email:email',
			[
				'value' => 'profile.phone',
				'label' => Yii::t('common', 'Phone number'),
				'format' => 'tel',
			],
			[
				'attribute' => 'phone_2',
				'value' => 'profile.phone_2',
				'label' => Yii::t('common', 'Phone number 2'),
				'format' => 'tel',
			],
			'created_at:datetime',
			[
				'class' => ActionColumn::class,
				'template' => $this->actionTemplate,
				'visibleButtons' => [
					'create-issue' => Yii::$app->user->can(Worker::PERMISSION_ISSUE_CREATE),
					'link' => Yii::$app->user->can(Worker::PERMISSION_ISSUE_LINK_USER),
				],
				'buttons' => [
					'create-issue' => static function (string $url, User $model): string {
						return Html::a(
							'<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>',
							Url::toRoute(['/issue/issue/create', 'customerId' => $model->id]),
							[
								'title' => Yii::t('backend', 'Create issue'),
								'aria-label' => Yii::t('backend', 'Create issue'),
							]);
					},
					'link' => static function (string $url, User $model) {
						return Html::a('<span class="glyphicon glyphicon-paperclip"></span>',
							['/issue/user/link', 'userId' => $model->id],
							[
								'title' => Yii::t('backend', 'Link to issue'),
								'aria-label' => Yii::t('backend', 'Link to issue'),
								'data-pjax' => '0',
							]);
					},
				],
			],
		];
	}
}
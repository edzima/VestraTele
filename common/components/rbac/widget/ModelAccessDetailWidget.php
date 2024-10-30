<?php

namespace common\components\rbac\widget;

use common\components\rbac\AccessPermission;
use common\components\rbac\ModelAccessManager;
use common\components\rbac\ModelRbacInterface;
use common\helpers\Html;
use common\helpers\Url;
use common\models\user\User;
use common\widgets\grid\ActionColumn;
use common\widgets\GridView;
use kartik\grid\ExpandRowColumn;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ArrayDataProvider;

class ModelAccessDetailWidget extends GridView {

	public ?ModelRbacInterface $model;
	public ?ModelAccessManager $accessManager = null;

	public string $type = AccessPermission::COMPARE_WITHOUT_APP_AND_ACTION;

	public $summary = false;

	public function init(): void {
		if (empty($this->caption)) {
			$this->caption = Yii::t('rbac', 'Access detail');
		}
		if (empty($this->dataProvider)) {
			if (empty($this->accessManager)) {
				if (empty($this->model)) {
					throw new InvalidConfigException('rbac or accessManager must be set');
				}
				$this->accessManager = $this->model->getModelAccess();
			}
			$models = $this->accessManager->getAccessPermissions($this->type);
			$this->dataProvider = new ArrayDataProvider([
				'allModels' => $models,
			]);
		}
		if (empty($this->columns)) {
			$this->columns = $this->defaultColumns();
		}
		parent::init();
	}

	protected function defaultColumns(): array {
		return [
			[
				'label' => Yii::t('rbac', 'App'),
				'attribute' => 'app',
				'value' => function (AccessPermission $model): string {
					return Yii::t('rbac', $model->app);
				},
			],
			[
				'label' => Yii::t('rbac', 'Action'),
				'attribute' => 'action',
				'value' => function (AccessPermission $model): string {
					return Yii::t('rbac', $model->action);
				},
			],
			[
				'label' => Yii::t('rbac', 'Roles'),
				'value' => function (AccessPermission $model): ?string {
					$names = $this->accessManager->getParentsRoles($model->name);
					if (empty($names)) {
						return null;
					}
					array_walk($names, function (&$value) {
						$value = Yii::t('rbac', $value);
					});

					return Html::ul($names, ['encode' => false]);
				},
				'format' => 'html',
			],
			[
				'label' => Yii::t('rbac', 'Permissions'),
				'value' => function (AccessPermission $model): ?string {
					$names = $this->accessManager->getParentsPermissions($model->name);
					if (empty($names)) {
						return null;
					}
					array_walk($names, function (&$value) {
						$value = Yii::t('rbac', $value);
					});
					return Html::ul($names, ['encode' => false]);
				},
				'format' => 'html',
			],
			[
				'label' => Yii::t('rbac', 'Assign Users'),
				'value' => function (AccessPermission $model): ?string {
					$ids = $this->accessManager->getUserIds($model->name);
					if (empty($ids)) {
						return null;
					}
					$names = User::getSelectList($ids, false);
					return Html::ul($names, ['encode' => false]);
				},
				'format' => 'html',
			],
			[
				'class' => ExpandRowColumn::class,
				'detail' => function (AccessPermission $model) {
					$names = array_merge(
						$this->accessManager->getParentsRoles($model->name),
						$this->accessManager->getParentsPermissions($model->name)
					);
					if (empty($names)) {
						return null;
					}
					$ids = User::getAssignmentIds($names, false);
					if (empty($ids)) {
						return null;
					}
					$names = User::getSelectList($ids, true);
					return Html::ul($names, ['encode' => false]);
				},
				'value' => function ($model, $key, $index) {
					return GridView::ROW_COLLAPSED;
				},
			],
			[
				'class' => ActionColumn::class,
				'template' => '{single-access}',
				'urlCreator' => function (string $action, AccessPermission $model) {
					$url = Url::to([
						$action,
						'id' => $model->modelId,
						'app' => $model->app,
						'action' => $model->action,
					]);
					return $url;
				},
				'buttons' => [
					'single-access' => function ($url, AccessPermission $model) {
						return Html::a(
							Html::icon('pencil'),
							$url
						);
					},
				],
			],
		];
	}
}

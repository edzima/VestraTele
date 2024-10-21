<?php

namespace common\components\rbac\widget;

use common\components\rbac\AccessPermission;
use common\components\rbac\ModelAccessManager;
use common\components\rbac\ModelRbacInterface;
use common\helpers\Html;
use common\widgets\GridView;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ArrayDataProvider;

class ModelAccessDetailWidget extends GridView {

	public ?ModelRbacInterface $model;
	public ?ModelAccessManager $accessManager = null;

	public string $type = AccessPermission::COMPARE_WITHOUT_APP_AND_ACTION;

	public function init(): void {
		if (empty($this->dataProvider)) {
			if (empty($this->accessManager)) {
				if (empty($this->model)) {
					throw new InvalidConfigException('rbac or accessManager must be set');
				}
				$this->accessManager = $this->model->getModelRbac();
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
				'value' => function (AccessPermission $model): string {
					$names = $this->accessManager->getParentsRoles($model->name);
					array_walk($names, function (&$value) {
						$value = Yii::t('rbac', $value);
					});

					return Html::ul($names, ['encode' => false]);
				},
				'format' => 'html',
			],
			[
				'label' => Yii::t('rbac', 'Permissions'),
				'value' => function (AccessPermission $model): string {
					$names = $this->accessManager->getParentsPermissions($model->name);
					array_walk($names, function (&$value) {
						$value = Yii::t('rbac', $value);
					});
					return Html::ul($names, ['encode' => false]);
				},
				'format' => 'html',
			],
		];
	}
}

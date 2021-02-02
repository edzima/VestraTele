<?php

namespace frontend\models;

use frontend\controllers\ArticleController;
use Yii;
use yii\base\Model;
use common\models\Menu;

/**
 * @inheritdoc
 */
class NavItem extends Model {

	public static function getNewsCategoryItems(array $models = null): array {
		return ArticleController::getMenuItems($models);
	}

	/**
	 * Generate menu items for yii\widgets\Menu
	 *
	 * @param null|array $models
	 * @return array
	 */
	public static function getMenuItems(array $models = null) {
		$items = [];
		if ($models === null) {
			$models = Menu::find()->where(['parent_id' => null])->with('childs')->orderBy(['sort_index' => SORT_ASC])->active()->all();
		}
		foreach ($models as $model) {
			$url = preg_match('/^(http:\/\/|https:\/\/)/', $model->url) ? $model->url : Yii::$app->request->baseUrl . '/' . ltrim($model->url, '/');
			$items[] = [
				'url' => $url,
				'label' => $model->label,
				'items' => self::getMenuItems($model->childs),
				'active' => $model->url === Yii::$app->request->pathInfo,
			];
		}

		return $items;
	}
}

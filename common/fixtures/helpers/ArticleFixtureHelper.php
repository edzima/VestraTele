<?php

namespace common\fixtures\helpers;

use common\fixtures\ArticleCategoryFixture;
use common\fixtures\ArticleFixture;
use Yii;

class ArticleFixtureHelper extends BaseFixtureHelper {

	public static function getDefaultDataDirPath(): string {
		return Yii::getAlias('@common/tests/_data/article/');
	}

	public static function fixtures(): array {
		return [
			'article.base' => [
				'class' => ArticleFixture::class,
				'dataFile' => static::getDataDirPath() . 'article.php',

			],
			'article.category' => [
				'class' => ArticleCategoryFixture::class,
				'dataFile' => static::getDataDirPath() . 'article_category.php',
			],
		];
	}
}

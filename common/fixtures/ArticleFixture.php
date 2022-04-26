<?php

namespace common\fixtures;

use common\models\Article;
use yii\test\ActiveFixture;

class ArticleFixture extends ActiveFixture {

	public $modelClass = Article::class;

	public $depends = [
		ArticleCategoryFixture::class,
		UserFixture::class,
	];
}

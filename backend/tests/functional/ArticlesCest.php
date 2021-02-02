<?php

namespace backend\tests\functional;

use backend\tests\Step\Functional\Manager;
use backend\tests\Step\Functional\NewsManager;
use common\fixtures\ArticleCategoryFixture;
use common\fixtures\UserFixture;

class ArticlesCest {

	public const ROUTE_INDEX = '/address/index';

	public function _fixtures() {
		return [
			'user' => [
				'class' => UserFixture::class,
				'dataFile' => codecept_data_dir() . 'user.php',
			],
			'articleCategory' => [
				'class' => ArticleCategoryFixture::class,
				'dataFile' => codecept_data_dir() . 'article_category.php',
			],
		];
	}

	public function checkWithoutPermission(Manager $I): void {
		$I->amLoggedIn();
		$I->dontseeMenuLink('Articles');
	}

	public function checkWithPermission(NewsManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Articles');
	}
	public function checkArticleSubmitNoData(NewsManager $I): void{
		$I->amLoggedIn();
		$I->amOnRoute('/article/create');
		$I->submitForm('#article-form', []);
		$I->seeValidationError('Title cannot be blank');
		$I->seeValidationError('Text cannot be blank');
		$I->seeValidationError('Category cannot be blank');
	}

	public function checkCreateArticleWithSimpleBody(NewsManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute('/article/create');

		$I->submitForm('#article-form', [
			'Article[title]' => "test",
			'Article[slug]' => "test",
			'Article[body]' => "test",
			'Article[status]' => 1,
			'Article[category_id]' => 1,
			'Article[published_at]' => "2020-01-28 10:00",
		]);
		$I->dontseeValidationError('Title cannot be blank');
		$I->dontseeValidationError('Text cannot be blank');
		$I->dontseeValidationError('Category cannot be blank');
	}

}

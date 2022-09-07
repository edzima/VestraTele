<?php

namespace backend\tests\functional;

use backend\controllers\ArticleController;
use backend\tests\Step\Functional\Manager;
use backend\tests\Step\Functional\NewsManager;
use common\fixtures\helpers\ArticleFixtureHelper;
use common\models\Article;

class ArticlesCest {

	/* @see ArticleController::actionIndex() */
	public const ROUTE_INDEX = '/article/index';
	/* @see ArticleController::actionCreate() */
	public const ROUTE_CREATE = '/article/create';

	public function _fixtures(): array {
		return ArticleFixtureHelper::fixtures();
	}

	public function checkMenuLinkWithoutPermission(Manager $I): void {
		$I->amLoggedIn();
		$I->dontseeMenuLink('Articles');
		$I->amOnRoute(static::ROUTE_INDEX);
		$I->seeResponseCodeIs(403);
	}

	public function checkWithPermission(NewsManager $I): void {
		$I->amLoggedIn();
		$I->seeMenuLink('Articles');
		$I->clickMenuSubLink('Articles');
		$I->seeInCurrentUrl(static::ROUTE_INDEX);
	}

	public function checkArticleSubmitNoData(NewsManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_CREATE);
		$I->submitForm('#article-form', []);
		$I->seeValidationError('Title cannot be blank');
		$I->seeValidationError('Text cannot be blank');
		$I->seeValidationError('Category cannot be blank');
	}

	public function checkCreateArticleWithSimpleBody(NewsManager $I): void {
		$I->amLoggedIn();
		$I->amOnRoute(static::ROUTE_CREATE);

		$I->submitForm('#article-form', [
			'Article[title]' => "test",
			'Article[slug]' => "test",
			'Article[body]' => "test",
			'Article[status]' => Article::STATUS_ACTIVE,
			'Article[show_on_mainpage]' => 1,
			'Article[category_id]' => 1,
			'Article[published_at]' => "2020-01-28 10:00",
		]);
		$I->dontseeValidationError('Title cannot be blank');
		$I->dontseeValidationError('Text cannot be blank');
		$I->dontseeValidationError('Category cannot be blank');
		$I->seeRecord(Article::class, [
			'title' => 'test',
			'slug' => 'test',
			'body' => 'test',
			'category_id' => 1,
			'show_on_mainpage' => 1,
			'status' => Article::STATUS_ACTIVE,
		]);
	}

}

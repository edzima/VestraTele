<?php

namespace backend\tests\acceptance;

use backend\tests\Step\acceptance\NewsManager;
use common\fixtures\ArticleCategoryFixture;
use common\fixtures\UserFixture;

/**
 * Class LoginCest
 */
class ArticlesCest {
	public const CREATE_URL = '/article/create';

	/**
	 * Load fixtures before db transaction begin
	 * Called in _before()
	 *
	 * @return array
	 * @see \Codeception\Module\Yii2::loadFixtures()
	 * @see \Codeception\Module\Yii2::_before()
	 */
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
	public function checkCreateSimpleNews(NewsManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(self::CREATE_URL);

		$I->fillField('Article[title]', 'test');
		$I->fillField('Article[slug]', 'test');
		$I->appendField('.redactor-editor', ' test');
		$I->appendField('Article[category_id]','1');
		$I->fillField('Article[published_at]', '2020-01-28 10:00');

		$I->click('#article-form button[type="submit"]');
		$I->wait(2);
		$I->dontSeeCurrentUrlEquals(self::CREATE_URL);
	}

	public function checkCreateNewsWithImageInBody(NewsManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(self::CREATE_URL);

		$I->click(['class' => 're-image']);
		$I->attachFile('input[type="file"]', 'test.png');
		$I->fillField('Article[title]', 'test');
		$I->fillField('Article[slug]', 'test');
		$I->appendField('.redactor-editor', ' test');
		$I->appendField('Article[category_id]','1');
		$I->fillField('Article[published_at]', '2020-01-28 10:00');
		$I->seeImageLoaded('img[data-verified="redactor"]');

		$I->click('#article-form button[type="submit"]');
		$I->wait(2);
		$I->dontSeeCurrentUrlEquals(self::CREATE_URL);
	}

	public function checkCreateNewsWithTxtAttachment(NewsManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(self::CREATE_URL);

		$I->click(['class' => 're-file']);
		$I->attachFile('input[type="file"]', 'test.txt');
		$I->click('.redactor-editor a');
		$I->click('.redactor-link-tooltip-action');
		$I->wait(1);
		$I->switchToNextTab();
		$I->dontSeeCurrentUrlEquals(self::CREATE_URL);
		$I->see("Test12345");
	}

	public function checkCreateNewsWithBlackListedAttachment(NewsManager $I): void {
		$I->amLoggedIn();
		$I->amOnPage(self::CREATE_URL);

		$I->click(['class' => 're-file']);
		$I->attachFile('input[type="file"]', 'test.js');
		$I->click('.redactor-editor a');
		$I->click('.redactor-link-tooltip-action');
		$I->wait(1);
		$I->switchToNextTab();
		$I->dontSeeCurrentUrlEquals(self::CREATE_URL);
		$I->dontSee("test_test");
		$I->see("404");
	}

}

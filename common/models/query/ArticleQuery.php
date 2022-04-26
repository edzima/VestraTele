<?php

namespace common\models\query;

use common\models\Article;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Article]].
 *
 * @see Article
 */
class ArticleQuery extends ActiveQuery {

	/**
	 * @return $this
	 */
	public function published(): self {
		$this->andWhere([Article::tableName() . '.status' => Article::STATUS_ACTIVE]);
		$this->andWhere(['<', Article::tableName() . '.published_at', time()]);

		return $this;
	}

	public function mainpage(): self {
		$this->andWhere('show_on_mainpage IS NOT NULL');
		$this->orderBy(['show_on_mainpage' => SORT_ASC]);
		return $this;
	}
}

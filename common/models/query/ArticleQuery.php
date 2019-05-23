<?php

namespace common\models\query;

use yii\db\ActiveQuery;
use common\models\Article;

/**
 * This is the ActiveQuery class for [[\common\models\Article]].
 *
 * @see \common\models\Article
 */
class ArticleQuery extends ActiveQuery {

	/**
	 * @return $this
	 */
	public function published() {
		$this->andWhere(['{{%article}}.status' => Article::STATUS_ACTIVE]);
		$this->andWhere(['<', '{{%article}}.published_at', time()]);

		return $this;
	}
}

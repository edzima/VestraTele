<?php

use backend\helpers\Html;
use common\models\issue\IssueType;

/* @var $this yii\web\View */
/* @var $model IssueType */

echo Html::dump(Yii::$app->issueTypeUser->getUsersIds($model->id));
echo Html::dump(Yii::$app->issueTypeUser->getChildren($model->id));
?>




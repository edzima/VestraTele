<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\UserProfile;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Users'), 'url' => ['users']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-default-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if ($profile->avatar_path) : ?>
            <img src="<?= Yii::getAlias('@storageUrl/avatars/' . $profile->avatar_path) ?>" class="img-thumbnail" alt>
        <?php else: ?>
            <img src="<?= Yii::$app->homeUrl . '/static/img/default.png' ?>" class="img-thumbnail" alt>
        <?php endif ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => Yii::t('frontend', 'Firstname'),
                'value' => $profile->firstname,
                'visible' => $profile->firstname !== null,
            ],
            [
                'attribute' => Yii::t('frontend', 'Lastname'),
                'value' => $profile->lastname,
                'visible' => $profile->lastname !== null,
            ],
            'action_at:datetime',
        ],
    ]) ?>

    <p>
        <?php if ($model->id !== Yii::$app->user->id) {
            echo Html::a(Yii::t('frontend', 'Send email'), ['message', 'id' => $model->id], ['class' => 'btn btn-success']);
        } ?>
    </p>
</div>

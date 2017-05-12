<?php

use yii\bootstrap\Html;
use yii\grid\GridView;
use common\models\User;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('backend', 'Users');
$this->params['breadcrumbs'][] = $this->title;

$typWok = ['0' => 'Przedstawiciel', '1' => 'Telemarketer'];
?>
<div class="user-index">

    <p>
        <?= Html::a(Yii::t('backend', 'Create user'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'username',
            // 'auth_key',
            // 'access_token',
            // 'password_hash',
            'email:email',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return User::statuses($model->status);
                },
                'filter' => User::statuses(),
            ],
            'ip',
            // 'created_at',
            // 'updated_at',
            'action_at:Datetime',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}',
            ],
        ],
    ]) ?>

</div>

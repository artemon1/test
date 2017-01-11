<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use app\models\Transfer;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$isNewUser = $user->isNewRecord;
$this->title = $isNewUser ? 'Список пользователей' : 'Пользователь: ' . $user->nikname;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= !$isNewUser ?
		DetailView::widget([
        'model' => $user,
        'attributes' => [
            'id',
            'nikname',
            'balance'
        ],
    ]) : ''; ?>
		<?= GridView::widget([
        'dataProvider' => $user->searchTransfers(Yii::$app->request->queryParams, $isNewUser),
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
						['attribute' => 'u_nikname', 'label' => 'Пользователь', 'visible' => $isNewUser],
						['attribute' => 'u_balance', 'label' => 'Баланс', 'visible' => $isNewUser],
						['attribute' => 'u_nikname', 'label' => 'От кого', 'visible' => !$isNewUser],
						['attribute' => 'u1_nikname', 'label' => 'Кому', 'visible' => !$isNewUser],
						['attribute' => 'amount', 'label' => 'Сумма', 'visible' => !$isNewUser],
						['attribute' => 'status',
						'label' => 'Статус',
						'value' => function($model) {
													$arr = [Transfer::STATUS_WAIT => 'Ожидание',
																	Transfer::STATUS_PAY => 'Оплачено',
																	Transfer::STATUS_CANCELLED => 'Отменено'];
													return $arr[$model['status']];
												},
						'visible' => !$isNewUser
						],
						['attribute' => 'time',
						'label' => 'Время',
						'format' => ['date', 'php:Y-m-d H:i:s'],
						'visible' => !$isNewUser
						],
            ['class' => 'yii\grid\ActionColumn',
						'visible' => !$isNewUser,
						'template' => '{pay-account}{cancel-pay-account}',
						'buttons' => ['pay-account' => function($url, $model) {
																						if(Yii::$app->user->identity->nikname==$model['u1_nikname'] &&
																							$model['status']==Transfer::STATUS_WAIT) {
																							return '&nbsp;&nbsp;&nbsp;' .
																							Html::a('Оплатить',
																							['/user/pay-account', 'id' => $model['t_id']],
																							['class' => 'btn-sm btn-primary', 'id' => 'transfer'.$model['t_id']]);
																						}
																					},
													'cancel-pay-account' => function($url, $model) {
																						if(Yii::$app->user->identity->nikname==$model['u1_nikname'] &&
																							$model['status']==Transfer::STATUS_WAIT) {
																							return '&nbsp;&nbsp;&nbsp;' .
																							Html::a('Отменить оплату',
																							['/user/cancel-pay-account', 'id' => $model['t_id']],
																							['class' => 'btn-sm btn-primary', 'id' => 'transfer-cancel'.$model['t_id']]);
																						}
																					}
						],
						],
        ],
    ]); ?>
</div>
<?php if(!$isNewUser): ?>
<div class="jumbotron">
  <p><?= Html::a('Перевести сумму', ['/user/transfer-out', 'id' => $user->id], ['class' => 'btn btn-primary', 'id' => 'transfer-out']) ?>
	 <?= Html::a('Выставить счет', ['/user/transfer-in', 'id' => $user->id], ['class' => 'btn btn-primary', 'id' => 'transfer-in']) ?></p>
</div>
<?php endif; ?>
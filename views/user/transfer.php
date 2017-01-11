<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = isset($out) ? 'Перевод суммы' : 'Выставить счет';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>
		
    <p>Пожалуйста, выберите пользователя, которому собираетесь <?=isset($out) ? 'перевести сумму' : 'выставить счет' ?> и укажите необходимую сумму</p>
		<div class="row">
			<div class="col-md-5">
				<?php $form = ActiveForm::begin(['id' => 'transfer']); ?>
		
					<?= $form->field($transfer, 'user')->dropDownList($users, ['prompt'=>'Выберите пользователя', 'id'=>'nik']) ?>
					
					<?php if(isset($out)) :?>
					<p>Если пользователя не существует в списке, ниже введите его имя для его добавления и зачисления ему нужной суммы</p>
					<?= $form->field($transfer, 'nikname')->textInput(['maxlength' => true, 'id'=>'nikname'])->label(false) ?>
					<?php endif;?>
			
					<?= $form->field($transfer, 'amount')->textInput(['maxlength' => true, 'id'=>'amount']) ?>
			
					<div class="form-group">
						<?= Html::submitButton(isset($out) ? 'Перевести сумму' : 'Выставить счет', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
					</div>
		
				<?php ActiveForm::end(); ?>
			</div>
		</div>
</div>

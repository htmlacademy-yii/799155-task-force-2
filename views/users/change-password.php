<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Password;

$user = Yii::$app->helpers->checkAuthorization();

?>
<head>
<style>
.regular-form {
  width: 440px;
}
</style>
</head>

<div class="center-block container--registration">
    <div class="regular-form">
        <?php $form = ActiveForm::begin([
            'id' => 'registration-form',
            'options' => ['class' => 'registration-form'],
            'enableClientValidation' => false,
            ]); ?>
            <h2 class="head-main head-regular">Изменение пароля</h2>
            <div class="half-wrapper">
                <div class="form-group">
                    <?= $form->field($model, 'userPasswordHash')->hiddenInput()->label(false); ?>
                    <?= $form->field($model, 'userPasswordOld')->
                        input('password')->label('Введите текущий пароль'); ?>
                    <?= $form->field($model, 'password_repeat')->
                        input('password')->label('Введите новый пароль'); ?>
                    <?= $form->field($model, 'password')->
                        input('password')->label('Повторите новый пароль'); ?>
                </div>
            </div>
                <div class="form-group">
                    <div class="control-label">
                        <?= Html::a('Отменить',
                            ['/edit-profile/' . $user->id,],
                            ['class' => 'button button--blue']
                        );?>
                        <button type="submit" class="button button--orange" form="registration-form"
                            name="replace" value="ok">Сменить</button>
                    </div>
                </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php
$js = <<<JS
var password = $('#password-userpassword2');
var form = $('#registration-form');
password.on('blur', function(evt) {
    var data = form.serialize();
    $.ajax({
        type: 'post',
        data: data,
        success: function(res, status) {
            console.log(res);
        },
        error: function(request, status, error) {
            console.log('error: ' + status);
        }
    });
});
JS;
$this->registerJs($js);
?>


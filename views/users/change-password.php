<?php

/* @var $this \yii\web\View */
/* @var $model Password */
/* @var $url string back URL */
/* @var $result bool управляет показом результата операции*/

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$user = Yii::$app->helpers->checkAuthorization();

?>
<head>
<style>
.regular-form {
  width: 440px;
}
.button--black {
  margin-left: auto;
  margin-right: 0;
  padding: 15px 40px;
}
</style>
</head>

<div class="center-block">
    <div class="regular-form">
        <?php $form = ActiveForm::begin([
            'id' => 'registration-form',
            'options' => ['class' => 'registration-form'],
            'enableClientValidation' => false,
            ]); ?>
            <h2 class="head-main head-regular">Изменение пароля</h2>
            <!-- Modal -->
            <div class="modal" tabindex="-1" id="modal" role="dialog">
                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2 id="label">Пароль заменён</h2>
                            <div><input type="hidden" id="url" value="<?=$url?>"></input></div>
                            <div><input type="hidden" id="result" value="<?=$result?>"></input></div>
                        </div>
                    </div>
                </div>
            </div>
            <!--Modal -->
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
            <div class="half-wrapper">
                <div class="control-label">
                    <?= Html::a(
                        'Отменить',
                        ['/edit-profile/' . $user->id,],
                        ['class' => 'button button--black']
                    );?>
                    <button type="submit" class="button button--black" form="registration-form"
                        name="replace" value="ok">Заменить</button>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php

$js = <<<JS
$(document).ready(function(){
    var modal = $('#modal');
    var url = $('#url').val();
    var result = $('#result').val();
    if (result) {
            modal.fadeIn(500);
            setTimeout(function () {
                modal.fadeOut(3000);
                $(location).attr('href', url);
            }, 3000);
        }
    });
JS;
$this->registerJs($js);
?>

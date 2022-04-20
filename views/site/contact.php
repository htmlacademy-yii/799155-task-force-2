<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model app\models\Contact */
/* @var $url string back URL */
/* @var $result bool управляет показом результата операции*/

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\captcha\Captcha;

$this->title = 'Контакт';
?>
<head>
<style>
.regular-form {
  width: 400px;
}
.button--black {
    margin-top: 20px;
    margin-bottom: 15px;
    padding: 15px 60px;
    margin-right: 500px;
}
</style>
</head>
<div class="center-block">
<div class="regular-form">
    <h1><?= Html::encode($this->title);?></h1>
        <!-- Modal -->
        <div class="overlay" id="overlay"></div>
        <div class="modal" tabindex="-1" id="modal" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <p id="label">Благодарим Вас за то, что обратились к нам.
                            Мы ответим Вам как можно скорее.</p>
                        <div><input type="hidden" id="url" value="<?=$url?>"></input></div>
                        <div><input type="hidden" id="result" value="<?=$model->sendOk?>"></input></div>
                    </div>
                </div>
            </div>
        </div>
        <!--Modal -->
        <p>
        Если у Вас есть деловые запросы или другие вопросы, пожалуйста,
        заполните следующую форму, чтобы связаться с нами.
        Спасибо.
        </p>
        <div class="form-group">
            <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
                <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'subject') ?>
                <?= $form->field($model, 'body')->textarea(['rows' => 6]) ?>
                <?= $form->field($model, 'verifyCode')->widget(
                    Captcha::className(),
                    [
                        'template' => '<div class="row">
                            <div class="col-lg-3">{image}</div>
                            <div class="col-lg-6">{input}</div>
                        </div>',
                    ]
                );?>
                <div class="form-group">
                    <button type="submit" class="button button--black" name="contact-button"
                        form="contact-form" value="ok">Отправить</button>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
</div>
</div>

<?php
$js = <<<JS
$(document).ready(function(){
    var modal = $('#modal');
    var url = $('#url').val();
    var overlay = $('#overlay');
    var result = $('#result').val();
    if (result) {
        overlay.fadeIn();
        modal.fadeIn(500);
        setTimeout(function () {
            modal.fadeOut(3000);
            overlay.fadeOut();
            $(location).attr('href', url);
        }, 5000);
    }
});
JS;
$this->registerJs($js);
?>
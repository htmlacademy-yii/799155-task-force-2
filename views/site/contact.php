<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $model app\models\Contact */

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\captcha\Captcha;

$this->title = 'Контакт';
?>
<style>
.regular-form {
  width: 400px;
}
</style>
<div class="regular-form">
    <h1><?= Html::encode($this->title);?></h1>
    <?php if ($model->sendOk) :?>
        <?php $form = ActiveForm::begin(['id' => 'modal-form']); ?>
            <div class="alert alert-success">
                <p>Благодарим вас за то, что обратились к нам. Мы ответим вам как можно скорее.</p>
            </div>
            <div class="form-group">
                <?= $form->field(
                        $model,
                        'sendOk',
                        ['labelOptions' => ['hidden' => 'hidden']]
                    )->hiddenInput();
                ?>
                <button type="submit" class="modal-button" name="modal-button"
                    form="modal-form" value="ok">Закрыть</button>
            </div>
        <?php ActiveForm::end(); ?>
    <?php else :?>
        <p>
        Если у вас есть деловые запросы или другие вопросы, пожалуйста,
        заполните следующую форму, чтобы связаться с нами.
        Спасибо.
        </p>

        <div class="form-group">
            <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>
                <?= $form->field($model, 'name')->textInput(['autofocus' => true]) ?>
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'subject') ?>
                <?= $form->field($model, 'body')->textarea(['rows' => 6]) ?>
                <?= $form->field($model, 'verifyCode')->widget(Captcha::className(),
                    [
                        'template' => '<div class="row">
                            <div class="col-lg-3">{image}</div>
                            <div class="col-lg-6">{input}</div>
                        </div>',
                    ]);
                ?>
                <div class="form-group">
                    <button type="submit" class="button" name="contact-button"
                        form="contact-form" value="ok">Отправить</button>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    <?php endif; ?>
</div>

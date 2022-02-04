<?php

/* @var $this yii\web\View */
/* @var $model данные пользователя */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

?>

<div class="center-block container--registration">
    <div class="regular-form">
        <?php $form = ActiveForm::begin([
            'id' => 'registration-form',
            'options' => ['class' => 'registration-form']
            ]); ?>
            <h3 class="head-main head-task">Вход</h3>
            <div class="half-wrapper">
                <div class="form-group">
                    <?php
                    echo $form->field(
                        $model,
                        'email',
                        [
                            'labelOptions' => [
                                'class' => 'control-label',
                            ],
                        ]
                    )->input('email')->hint('Введите адрес')->label('Электронная почта');
                    ?>
                </div>
                <div class="form-group">
                    <?php
                    echo $form->field(
                        $model,
                        'password',
                        [
                            'labelOptions' => [
                                'class' => 'control-label',
                            ],
                        ]
                    )->input('password')->hint('Введите пароль')->label('Пароль');
                    ?>
                </div>
            </div>
            <div class="form-group">
            <?= Html::submitButton('Войти', ['class' => 'button button--blue']) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

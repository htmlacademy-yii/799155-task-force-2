<?php

/* @var $this yii\web\View */
/* @var $model регистрационная информация */
/* @var $cities перечень городов*/

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

?>

<div class="center-block container--registration">
    <div class="regular-form">
        <?php $form = ActiveForm::begin(['id' => 'registration-form', 'options' => ['class' => 'registration-form']]); ?>
            <h3 class="head-main head-task">Регистрация нового пользователя</h3>
            <div class="form-group">
                <?php
                echo $form->field($model, 'name',
                    [
                        'labelOptions' => [
                            'class' => 'control-label',
                        ],
                    ]
                )->textInput()->hint('Введите имя')->label('Ваше имя');
                ?>
            </div>
            <div class="half-wrapper">
                <div class="form-group">
                    <?php
                    echo $form->field($model, 'email',
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
                        $options = [
                            'prompt' => 'Выберите город',
                        ];
                        echo $form->field(
                            $model,
                            'city_name',
                            [
                                'labelOptions' => [
                                    'class' => 'control-label',
                                    'label' => 'Ваш город',
                                ]
                            ]
                        )->dropDownList($cities, $options);
                        ?>
                </div>
            </div>
            <div class="form-group">
                <?php
                echo $form->field($model, 'password_repeat',
                    [
                        'labelOptions' => [
                            'class' => 'control-label',
                        ],
                    ]
                )->input('password')->hint('Введите пароль')->label('Пароль');
                    ?>
            </div>
            <div class="form-group">
                <?php
                echo $form->field($model, 'password',
                    [
                        'labelOptions' => [
                            'class' => 'control-label',
                        ],
                    ]
                )->input('password')->hint('Повторите пароль')->label('Пароль');
                    ?>
            </div>
            <div class="form-group">
                <?php
                    $options = [
                        'label' => 'Я собираюсь откликаться на заказы',
                        'uncheck' => false,
                    ];
                    echo $form->field(
                        $model,
                        'contractor',
                        [
                            'labelOptions' => [
                                'class' => 'head-card',
                            ],
                            'template' => '{input}<label class="control-label" 
                                        for="registration-contractor">{label}</label>',
                        ]
                    )->checkbox($options, false);
                    ?>
            </div>
            <div class="form-group">
                <?= Html::submitButton('Создать аккаунт', ['class' => 'button button--blue']) ?>
            </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<?php

/* @var $this yii\web\View */
/* @var $model регистрационная информация */
/* @var $categories перечень категорий*/

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="add-task-form regular-form">
    <?php $form = ActiveForm::begin(['id' => 'add-task-form']); ?>
        <h3 class="head-main head-main">Публикация нового задания</h3>
        <div class="form-group">
            <?php echo $form->field(
                $model,
                'name',
                [
                    'labelOptions' => [
                        'class' => 'control-label',
                    ],
                ]
            )->input('text')->hint('Опишите суть работы')->label('Название');?>
        </div>
        <div class="form-group">
            <?php echo $form->field(
                $model,
                'description',
                [
                    'labelOptions' => [
                        'class' => 'control-label',
                    ],
                ]
            )->textarea()->hint('Опишите задание пдробнее')->label('Подробности задания');?>
        </div>
        <div class="form-group">
            <?php $options = [
                    'prompt' => 'Выберите категорию',
                ];
                echo $form->field(
                    $model,
                    'category',
                    [
                        'labelOptions' => [
                            'class' => 'control-label',
                            'label' => 'Категория задания',
                        ]
                    ]
                )->dropDownList($categories, $options);?>
        </div>
        <div class="half-wrapper">
            <div class="form-group">
                <?php echo $form->field(
                    $model,
                    'city',
                    [
                        'labelOptions' => [
                            'class' => 'control-label',
                        ],
                        'enableClientValidation' => true,
                    ]
                )->input('text')->hint('Укажите город')->label('Город');
                echo $form->field(
                    $model,
                    'latitude',
                    [
                        'labelOptions' => [
                            'hidden' => 'hidden',
                        ],
                    ]
                )->hiddenInput();
                echo $form->field(
                    $model,
                    'longitude',
                    [
                        'labelOptions' => [
                            'hidden' => 'hidden',
                        ],
                    ]
                )->hiddenInput();?>
            </div>
            <div class="form-group">
                <?php echo $form->field(
                    $model,
                    'district',
                    [
                        'labelOptions' => [
                            'class' => 'control-label',
                        ],
                    ]
                )->input('text')->hint('Укажите район')->label('Район');?>
            </div>
            <div class="form-group">
                <?php echo $form->field(
                    $model,
                    'street',
                    [
                        'labelOptions' => [
                            'class' => 'control-label',
                        ],
                    ]
                )->input('text')->hint('Укажите улицу')->label('Улица');?>
            </div>
        </div>
        <div class="half-wrapper">
            <div class="form-group">
                <?php echo $form->field(
                    $model,
                    'budget',
                    [
                        'labelOptions' => [
                            'class' => 'control-label',
                        ],
                    ]
                )->input('number')->hint('Укажите стоимость работы')->label('Бюджет');?>
            </div>
            <div class="form-group">
                <?php echo $form->field(
                    $model,
                    'deadline',
                    [
                        'labelOptions' => [
                            'class' => 'control-label',
                        ],
                    ]
                )->input('date')->hint('Укажите дату')->label('Срок исполнения');?>
            </div>
        </div>
        <div class="form-group">
            <?= $form->field(
                $model,
                'files[]',
                [
                    'labelOptions' => [
                        'class' => 'control-label',
                        'label' => 'Дополнительные файлы',
                    ],
                ]
            )->fileInput(['multiple' => true,]);?>
        </div>
        <div class="bio-info form-group">
            <?= Html::submitInput('Опубликовать', ['class' => 'button button--blue']) ?>
        </div>
    <?php ActiveForm::end(); ?>
</div>

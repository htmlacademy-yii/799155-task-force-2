<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use app\models\Categories;

?>
<div class="left-column">
    <h3 class="head-main head-task">Новые задания</h3>
    <?php foreach ($tasks as $task) : ?>
        <div class="task-card">
        <div class="header-task">
            <a  href="#" class="link link--block link--big"><?=Html::encode($task->name)?></a>
            <p class="price price--task"><?=Html::encode($task->budget)?> ₽</p>
        </div>
        <p class="info-text"><span class="current-time">
            <?=Yii::$app->helpers->getTimeStr(Html::encode($task->add_date))?></span>
        </p>
        <p class="task-text"><?=Html::encode($task->description)?>
        </p>
        <div class="footer-task">
            <p class="info-text town-text"><?=Html::encode($task->city . ', ' . $task->street)?></p>
            <p class="info-text category-text"><?=Html::encode($task->category)?></p>
            <a href="#" class="button button--black">Смотреть Задание</a>
        </div>
    </div>
    <?php endforeach; ?>
    <div class="pagination-wrapper">
        <ul class="pagination-list">
            <li class="pagination-item mark">
                <a href="#" class="link link--page"></a>
            </li>
            <li class="pagination-item">
                <a href="#" class="link link--page">1</a>
            </li>
            <li class="pagination-item pagination-item--active">
                <a href="#" class="link link--page">2</a>
            </li>
            <li class="pagination-item">
                <a href="#" class="link link--page">3</a>
            </li>
            <li class="pagination-item mark">
                <a href="#" class="link link--page"></a>
            </li>
        </ul>
    </div>
</div>
<div class="right-column">
    <div class="right-card black">
        <?php $form = ActiveForm::begin(['id' => 'search-form', 'options' => ['class' => 'search-form']]); ?>
            <div class="form-group">
            <?php
                $this->params = is_array($categories->categoriesCheckArray) ?
                                            $categories->categoriesCheckArray : [];
                echo $form->field(
                    $categories,
                    'categoriesCheckArray',
                    [
                        'labelOptions' => [
                            'class' => 'head-card',
                        ],
                    ]
                )->checkboxList(
                    $categoryNames[Categories::MAIN_CATEGORIES],
                    [
                        'separator' => '<br>',
                        'item' => function ($index, $label, $name, $checked, $value) {
                            if (in_array($value, $this->params) === true) {
                                    $checked = 'checked';
                            }
                            return "<span><input type='checkbox' {$checked} name='{$name}' 
                                value='{$value}' id='{$index}'>
                                <label class='control-label' for='{$index}'>{$label}</label></span>";
                        },
                        'unselect' => Categories::CATEGORIES_NOT_SELECTED,
                    ]
                );
                ?>
            </div>
            <h4 class="head-card">Дополнительно</h4>
            <div class="form-group">
            <?php
                $options = [
                    'label' => $categoryNames[Categories::ADD_CONDITION],
                    'uncheck' => Categories::NO_ADDITION_SELECTED,
                ];
                echo $form->field(
                    $categories,
                    'additionCategoryCheck',
                    [
                        'labelOptions' => [
                            'class' => 'head-card',
                        ],
                        'template' => '{input}<label class="control-label" 
                                    for="categories-additioncategorycheck">{label}</label>',
                    ]
                )->checkbox($options, false);
                ?>
            </div>
            <h4 class="head-card"> </h4>
            <div class="form-group">
            <?php
                $options = [
                    'prompt' => 'Выберите период',
                ];
                echo $form->field(
                    $categories,
                    'period',
                    [
                        'labelOptions' => [
                            'class' => 'head-card',
                        ]
                    ]
                )->dropDownList($categoryNames[Categories::PERIODS], $options);
                ?>
            </div>
            <?= Html::submitButton('Искать', ['class' => 'button button--blue']) ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>

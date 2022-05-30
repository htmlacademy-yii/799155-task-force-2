<?php

/* @var $this yii\web\View */
/* @var $tasks массив заданий*/
/* @var $categories Categories*/
/* @var $categoryNames array список названий категорий */
/* @var $page int номер страницы пгинации*/

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\widgets\LinkPager;
use app\models\Categories;

$title = 'Новые задания';
$urls = [
    '/tasks' => $title,
    '/tasks/index' => $title,
    '/my-tasks' => 'Мои задания',
];

if (strstr(Url::current(), 'category') === false) {
    foreach ($urls as $key => $value) {
        if (strstr(Url::current(), $key) !== false) {
            $title = $value;
            break;
        }
    }
} else {
    $title = $categoryNames[Categories::MAIN_CATEGORIES][$categories->categoriesCheckArray[0]];
    $title .= ' (новые задания)';
}

?>
<div class="left-column">
    <h3 class="head-main head-task"><?=Html::encode($title)?></h3>
    <?php foreach ($tasks as $task) : ?>
    <div class="task-card">
        <div class="header-task">
            <a  href=<?='/task/' . Html::encode($task->id)?>
                class="link link--block link--big"><?=Html::encode($task->name)?></a>
            <?php if (!empty($task->budget)) :?>
                <p class="price price--task"><?=Html::encode($task->budget)?> ₽</p>
            <?php endif;?>
        </div>
        <p class="info-text"><span class="current-time">
            <?=Yii::$app->helpers->getTimeStr(Html::encode($task->add_date))?></span>
        </p>
        <p class="task-text"><?=Html::encode($task->description)?>
        </p>
        <div class="footer-task">
            <p class="info-text town-text"><?=Html::encode($task->city . ', ' . $task->street)?></p>
            <p class="info-text category-text"><?=Html::encode($task->category)?></p>
            <a href=<?='/task/' . Html::encode($task->id)?>
                class="button button--black">Смотреть задание</a>
        </div>
    </div>
    <?php endforeach; ?>
    <?php if ($pages->getPageCount() > 1) :?>
    <div class="pagination-wrapper">
        <ul class="pagination-list">
            <li class="pagination-item mark">
                <a href=<?='/tasks/' . ($pages->getPage() > 0 ? $pages->getPage() : '#')?>
                    class="link link--page"></a>
            </li>
            <?php for ($page = 1; $page <= $pages->getPageCount(); $page++) :?>
                <li class="pagination-item 
                    <?=($page === $pages->getPage() + 1) ? 'pagination-item--active' : ''?>">
                    <a href=<?='/tasks/' . $page?> class="link link--page"><?=$page?></a>
                </li>
            <?php endfor;?>
            <li class="pagination-item mark">
                <a href=<?='/tasks/' .
                    ($pages->getPage() < $pages->getPageCount() - 1 ? $pages->getPage() + 2 : '#')?>
                    class="link link--page"></a>
            </li>
        </ul>
    </div>
    <?php endif;?>
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
                $options = [
                    'label' => $categoryNames[Categories::MORE_CONDITION],
                    'uncheck' => Categories::NO_ADDITION_SELECTED,
                ];
                echo $form->field(
                    $categories,
                    'moreConditionCheck',
                    [
                        'labelOptions' => [
                            'class' => 'head-card',
                        ],
                        'template' => '{input}<label class="control-label" 
                                    for="categories-moreconditioncheck">{label}</label>',
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

<?php

use app\models\Task;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\LinkPager;

$link = substr(Url::current(), 0, strpos(Url::current(), '&', -1) - 1);
?>

<div class="left-menu">
    <h3 class="head-main head-task">Мои задания</h3>
    <ul class="side-menu-list">
        <?php foreach (Task::FILTER_LINKS[$contr] as $key => $value) :?>
        <li class="side-menu-item
            <?=strstr(Url::current(), $key) === false ? '' : 'side-menu-item--active';?>">
            <a href=<?='/my-tasks/' . Html::encode($key) . '&1'?>
                class="link link--nav"><?=Html::encode($value[0])?></a>
        </li>
        <?php endforeach;?>
    </ul>
</div>
<div class="left-column left-column--task">
    <h3 class="head-main head-regular"><?=Html::encode(Task::FILTER_LINKS[$contr][$code][1])?></h3>
    <?php foreach ($tasks as $task) :?>
    <div class="task-card">
            <div class="header-task">
            <a href=<?='/task/' . Html::encode($task->id)?>
                class="link link--block link--big"><?=Html::encode($task->name)?></a>
            <p class="price price--task"><?=Html::encode($task->budget) . ' ₽'?></p>
        </div>
        <p class="info-text"><span class="current-time">
            <?=Yii::$app->helpers->getTimeStr(Html::encode($task->add_date));?></span></p>
        <p class="task-text"><?=Html::encode($task->description)?></p>
        <div class="footer-task">
            <?php if (!empty($task->city)) :?>
            <p class="info-text town-text"><?=Html::encode($task->city)?></p>
            <?php endif;?>
            <p class="info-text category-text"><?=Html::encode($task->category)?></p>
            <a href=<?='/task/' . Html::encode($task->id)?> class="button button--black">Смотреть задание</a>
        </div>
    </div>
    <?php endforeach;?>
    <?php if ($pages->getPageCount() > 1) :?>
    <div class="pagination-wrapper">
        <ul class="pagination-list">
            <li class="pagination-item mark">
                <?php if ($pages->getPage() > 1) :?>
                    <a href=<?=$link . $pages->getPage();?> class="link link--page"></a>
                <?php else :?>
                    <a href=<?=$link . '1'?> class="link link--page"></a>
                <?php endif; ?>
            </li>
            <?php for ($page = 1; $page <= $pages->getPageCount(); $page++) :?>
                <li class="pagination-item 
                    <?=($page === $pages->getPage() + 1) ? 'pagination-item--active' : ''?>">
                    <a href=<?=$link . $page?> class="link link--page"><?=$page?></a>
                </li>
            <?php endfor;?>
            <li class="pagination-item mark">
            <?php if ($pages->getPage() < $pages->getPageCount() - 1) :?>
                    <a href=<?=$link . ($pages->getPage() + 2);?> class="link link--page"></a>
            <?php else :?>
                    <a href=<?=$link . ($pages->getPage() + 1)?> class="link link--page"></a>
            <?php endif; ?>
            </li>
        </ul>
    </div>
    <?php endif;?>
</div>

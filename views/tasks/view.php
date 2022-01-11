<?php

/* @var $this yii\web\View */
/* @var $task задание */
/* @var $replies отклики на задание */

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Task;

?>
<div class="left-column">
    <div class="head-wrapper">
        <h3 class="head-main"><?=Html::encode($task->name)?></h3>
        <p class="price price--big"><?=Html::encode($task->budget)?> ₽</p>
    </div>
    <p class="task-description">
        <?=Html::encode($task->description)?>
    </p>
    <?php if ($task->status === Task::STATUS_NEW) :?>
        <a href="#" class="button button--blue">Откликнуться на задание</a>
    <?php elseif ($task->status === Task::STATUS_ON_DEAL) :?>
        <a href="#" class="button button--blue">Отказаться от задания</a>
    <?php endif; ?>
    <div class="task-map">
        <img class="map" src=<?=Url::to('/img/map.png', true);?> width="725" height="346" alt="Адрес задания">
        <p class="map-address town"><?=Html::encode($task->city)?></p>
        <p class="map-address"><?=Html::encode($task->street)?></p>
    </div>
    <h4 class="head-regular">Отклики на задание</h4>
    <?php foreach ($replies as $reply) : ?>
        <div class="response-card">
            <img class="customer-photo" src=<?=Url::to($reply->avatar, true);?>
                width="146" height="156" alt="Фото заказчиков">
            <div class="feedback-wrapper">
                <a href="<?='/user/' . $reply->contr_id?>" class="link link--block link--big">
                    <?=$reply->contractor?>
                </a>
                <div class="response-wrapper">
                    <div class="stars-rating small">
                        <?php foreach ($reply->rating as $value) :?>
                            <?= $value ? '<span class="fill-star">&nbsp;</span>' : '<span>&nbsp;</span>'?>
                        <?php endforeach; ?>
                    </div>
                    <p class="reviews"><?=$reply->reviews . ' ' .
                        Yii::$app->helpers->getNounPluralForm($reply->reviews, 'отзыв', 'отзыва', 'отзывов')?></p>
                </div>
                <p class="response-message">
                    <?=$reply->comment?>
                </p>

            </div>
            <div class="feedback-wrapper">
                <p class="info-text"><span class="current-time">
                    <?=Yii::$app->helpers->getTimeStr(Html::encode($reply->add_date))?></span>
                </p>
                <p class="price price--small"><?=$reply->price . ' ₽'?></p>
            </div>
            <div class="button-popup">
                <a href="#" class="button button--blue button--small">Принять</a>
                <a href="#" class="button button--orange button--small">Отказать</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<div class="right-column">
    <div class="right-card black info-card">
        <h4 class="head-card">Информация о задании</h4>
        <dl class="black-list">
            <dt>Категория</dt>
            <dd>
                <a href="<?='/category/' . $task->cat_id?>" class="link link--small">
                    <?=Html::encode($task->category)?>
                </a>
            </dd>
            <dt>Дата публикации</dt>
            <dd><?=Yii::$app->helpers->getTimeStr(Html::encode($task->add_date))?></dd>
            <dt>Срок выполнения</dt>
            <dd><?=Yii::$app->helpers->ruDate(Html::encode($task->deadline))?></dd>
            <dt>Статус</dt>
            <dd><?=Html::encode(Task::TASK_DESCR[$task->status])?></dd>
        </dl>
    </div>
    <div class="right-card white file-card">
        <h4 class="head-card">Файлы задания</h4>
        <ul class="enumeration-list">
            <?php foreach ($docs as $doc) :?>
                <li class="enumeration-item">
                <a href="#" class="link link--block link--clip"><?=$doc->link?></a>
                <p class="file-size"><?=$doc->size?> Кб</p>
            </li>
            <?php endforeach;?>
        </ul>
    </div>
</div>

<?php

/* @var $this yii\web\View */
/* @var $task задание*/

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\helpers\Url;

?>
<div class="left-column">
    <div class="head-wrapper">
        <h3 class="head-main"><?=Html::encode($task->name)?></h3>
        <p class="price price--big"><?=Html::encode($task->budget)?> ₽</p>
    </div>
    <p class="task-description">
        <?=Html::encode($task->description)?>
    </p>
    <a href="#" class="button button--blue">Откликнуться на задание</a>
    <div class="task-map">
        <img class="map" src=<?=Url::to('/img/map.png', true);?> width="725" height="346" alt="Адрес задания">
        <p class="map-address town"><?=Html::encode($task->city)?></p>
        <p class="map-address"><?=Html::encode($task->street)?></p>
    </div>
    <h4 class="head-regular">Отклики на задание</h4>
    <div class="response-card">
        <img class="customer-photo" src=<?=Url::to('/img/man-glasses.png', true);?> width="146" height="156" alt="Фото заказчиков">
        <div class="feedback-wrapper">
            <a href="#" class="link link--block link--big">Астахов Павел</a>
            <div class="response-wrapper">
                <div class="stars-rating small"><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span>&nbsp;</span></div>
                <p class="reviews">2 отзыва</p>
            </div>
            <p class="response-message">
                Могу сделать всё в лучшем виде. У меня есть необходимый опыт и инструменты.
            </p>

        </div>
        <div class="feedback-wrapper">
            <p class="info-text"><span class="current-time">25 минут </span>назад</p>
            <p class="price price--small">3700 ₽</p>
        </div>
        <div class="button-popup">
            <a href="#" class="button button--blue button--small">Принять</a>
            <a href="#" class="button button--orange button--small">Отказать</a>
        </div>
    </div>
    <div class="response-card">
        <img class="customer-photo" src=<?=Url::to('/img/man-sweater.png', true);?> width="146" height="156" alt="Фото заказчиков">
        <div class="feedback-wrapper">
            <a href="#" class="link link--block link--big">Дмитриев Андрей</a>
            <div class="response-wrapper">
                <div class="stars-rating small"><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span class="fill-star">&nbsp;</span><span>&nbsp;</span></div>
                <p class="reviews">8 отзывов</p>
            </div>
            <p class="response-message">
                Примусь за выполнение задания в течение часа, сделаю быстро и качественно.
            </p>

        </div>
        <div class="feedback-wrapper">
            <p class="info-text"><span class="current-time">2 часа </span>назад</p>
            <p class="price price--small">1999 ₽</p>
        </div>
        <div class="button-popup">
            <a href="#" class="button button--blue button--small">Принять</a>
            <a href="#" class="button button--orange button--small">Отказать</a>
        </div>
    </div>
</div>
<div class="right-column">
    <div class="right-card black info-card">
        <h4 class="head-card">Информация о задании</h4>
        <dl class="black-list">
            <dt>Категория</dt>
            <dd><?=Html::encode($task->category)?></dd>
            <dt>Дата публикации</dt>
            <dd><?=Yii::$app->helpers->getTimeStr(Html::encode($task->add_date))?></dd>
            <dt>Срок выполнения</dt>
            <dd><?=Yii::$app->helpers->ruDate(Html::encode($task->deadline))?></dd>
            <dt>Статус</dt>
            <dd><?=Html::encode($task->status)?></dd>
        </dl>
    </div>
    <div class="right-card white file-card">
        <h4 class="head-card">Файлы задания</h4>
        <ul class="enumeration-list">
            <li class="enumeration-item">
                <a href="#" class="link link--block link--clip">my_picture.jpg</a>
                <p class="file-size">356 Кб</p>
            </li>
            <li class="enumeration-item">
                <a href="#" class="link link--block link--clip">information.docx</a>
                <p class="file-size">12 Кб</p>
            </li>
        </ul>
    </div>
</div>

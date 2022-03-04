<?php

/* @var $this yii\web\View */
/* @var $user данные пользователя*/

use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="left-column">
    <h3 class="head-main"><?=Html::encode($user->name)?></h3>
    <div class="user-card">
        <div class="photo-rate">
            <img class="card-photo" src=<?=Url::to($user->avatar, true);?>
                width="191" height="190" alt="Фото пользователя">
            <div class="card-rate">
                <div class="stars-rating big">
                    <?php foreach ($user->stars as $value) :?>
                        <?= $value ? '<span class="fill-star">&nbsp;</span>' : '<span>&nbsp;</span>'?>
                    <?php endforeach; ?>
                </div>
                <span class="current-rate"><?=$user->rating?></span>
            </div>
        </div>
        <p class="user-description">
            <?=$user->about_info?>
        </p>
    </div>
    <div class="specialization-bio">
        <div class="specialization">
            <p class="head-info">Специализации</p>
            <ul class="special-list">
                <?php foreach ($user->categories as $category) :?>
                    <li class="special-item">
                        <a href=<?='/category/' . $category->id?> class="link link--regular"><?=$category->name?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="bio">
            <p class="head-info">Био</p>
            <p class="bio-info">
                <span class="country-info">Россия</span>,
                <span class="town-info"><?=$user->city?></span>,
                <span class="age-info"><?=Yii::$app->helpers->getAge($user->born_date)?></span> лет
            </p>
        </div>
    </div>
    <h4 class="head-regular" <?= count($reviews) ? '' : 'hidden';?>>Отзывы заказчиков</h4>
    <?php foreach ($reviews as $review) :?>
    <div class="response-card">
        <img class="customer-photo" src=<?=Url::to($review->avatar, true);?>
            width="120" height="127" alt="Фото заказчиков">
        <div class="feedback-wrapper">
            <p class="feedback"><?=$review->comment?></p>
            <p class="task">Задание «<a href=<?='/task/' . $review->task_id?> 
                class="link link--small"><?=$review->name?></a>» <?=$review->status?>
            </p>
        </div>
        <div class="feedback-wrapper">
            <div class="stars-rating small">
                <?php foreach ($review->stars as $value) :?>
                        <?= $value ? '<span class="fill-star">&nbsp;</span>' : '<span>&nbsp;</span>'?>
                <?php endforeach; ?>
            </div>
            <div class="feedback">
                <p class="info-text">
                    <span class="current-time">
                        <?=Yii::$app->helpers->getTimeStr(Html::encode($review->add_date))?>
                    </span>
                </p>
            </div>
        </div>
    </div>
    <?php endforeach; ?> <!--- foreach ($reviews as $review) ---->
</div>
<div class="right-column">
    <div class="right-card black">
        <h4 class="head-card">Статистика исполнителя</h4>
        <dl class="black-list">
                <dt>Всего заказов</dt>
                <dd><?="выполнено: $user->doneCounter, провалено: $user->refuseCounter"?></dd>
                <dt>Место в рейтинге</dt>
                <dd><?=$user->position . ' место'?></dd>
                <dt>Дата регистрации</dt>
                <dd><?=Yii::$app->helpers->ruDate(Html::encode($user->add_date))?></dd>
                <dt>Статус</dt>
                <dd><?=$user->status?></dd>
        </dl>
    </div>
    <div class="right-card white">
        <h4 class="head-card">Контакты</h4>
        <ul class="enumeration-list">
            <?php if ($user->phone) :?>
            <li class="enumeration-item">
                <a href="#" class="link link--block link--phone"><?=$user->phone?></a>
            </li>
            <?php endif; ?>
            <li class="enumeration-item">
                <a href="#" class="link link--block link--email"><?=$user->email?></a>
            </li>
            <?php if ($user->messenger) :?>
            <li class="enumeration-item">
                <a href="#" class="link link--block link--tg"><?=$user->messenger?></a>
            </li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<?php

/* @var $this yii\web\View */
/* @var $task задание */
/* @var $replies отклики на задание */

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Task;
use yii\bootstrap4\Modal;
use yii\bootstrap4\ActiveForm;
use app\models\Reply;

$user = Yii::$app->helpers->checkAuthorization();
//прячем вызов модального диалога, если среди авторов
//откликов на задание есть активный пользователь
$hideReply = false;
foreach ($replies as $reply) {
    if ($reply->contr_id === $user->id) {
        $hideReply = true;
        break;
    }
}
?>

<?php if ($geocode !== null) :?>
<head>
<script type="text/javascript">
        ymaps.ready(init);
        function init() {
            var myMap = new ymaps.Map(
                "yandexmap",
                {
                    center: [<?=$geocode['lat']?>, <?=$geocode['lon']?>],
                    zoom: 15
                },
                {
                    searchControlProvider: 'yandex#search'
                });
            var placeMark = new ymaps.Placemark(
                myMap.getCenter(), 
                {
                    iconCaption: 'Место задания',
                    balloonContent: 'Место задания'
                }, 
                {
                    preset: 'islands#redDotIconWithCaption'
                });
            var myPlace = new ymaps.GeoObject(
                {   // Описание геометрии.
                    geometry: {
                        type: "Point",
                        coordinates: [<?=$geocode['lat']?>, <?=$geocode['lon']?>]
                    },
                    // Свойства.
                    properties: {
                        // Контент метки.
                        iconContent: 'Место задания',
                    }
                }, 
                {   // Опции.
                    // Иконка метки будет растягиваться под размер ее содержимого.
                    preset: 'islands#redStretchyIcon',
                    // Метку можно перемещать.
                    draggable: false
                });
                myMap.geoObjects.add(placeMark);
        }
    </script>
</head>
<?php endif;?>

<div class="left-column">
    <div class="head-wrapper">
        <h3 class="head-main"><?=Html::encode($task->name) . ' (' . Task::TASK_DESCR[$task->status] . ')'?></h3>
        <p class="price price--big"><?=empty($task->budget) ? '' : Html::encode($task->budget) . ' ₽'?></p>
    </div>
    <p class="task-description">
        <?=Html::encode($task->description)?>
    </p>
    <?php if ($user->contractor) :?>
        <!-- Рисуем модальное окно для исполнителя и формирования отклика на новое задание -->
        <?php if ($task->status === Task::STATUS_NEW and !$hideReply) :?>
            <?php Modal::begin([
                    'title' => '<h2>Отправка отклика</h2>',
                    'toggleButton' => [
                        'label' => 'Откликнуться на задание',
                        'tag' => 'button',
                        'class' => 'button button--blue',
                    ],
                    'footer' => $task->name,
                ]);
            ?>
            <?php $form = ActiveForm::begin(['id' => 'modal-form']); ?>
                <?= $form->field($reply, 'comment')->textarea(['autofocus' => true]) ?>
                <?= $form->field($reply, 'price')->input('number') ?>
                <div class="form-group">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Отменить</button>
                    <button type="submit" class="btn btn-primary"
                        form="modal-form" name="reply" value="ok">Отправить</button>
                </div>
            <?php ActiveForm::end(); ?>
            <?php Modal::end(); ?>
        <!-- Модальное окно для исполнителя для отказа от задания -->
        <?php elseif ($task->status === Task::STATUS_ON_DEAL and $user->id === $task->contr_id) :?>
            <?php Modal::begin([
                    'title' => '<h2>Подвердите отказ от задания</h2>',
                    'toggleButton' => [
                        'label' => 'Отказ от задания',
                        'tag' => 'button',
                        'class' => 'button button--blue',
                    ],
                    'footer' => $task->name,
                ]);
            ?>
            <?php $form = ActiveForm::begin(['id' => 'modal-form']); ?>
                <?= $form->field($reply, 'comment')->textarea(['autofocus' => true]) ?>
                <div class="form-group">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">Вернуться</button>
                    <button type="submit" class="btn btn-primary"
                        form="modal-form" name="refuse" value="ok">Отказаться</button>
                </div>
            <?php ActiveForm::end(); ?>
            <?php Modal::end(); ?>
        <?php endif; ?>
    <?php endif; ?>
    <!-- Рисуем модальное окно для заказчика и формирования отзыва о работе -->
    <?php if ($user->id === $task->custom_id) :?>
        <?php if ($task->status === Task::STATUS_NEW) :?>
            <a href="<?='/cancel/' . $task->id?>" class="button button--blue">Отменить задание</a>
        <?php elseif ($task->status === Task::STATUS_ON_DEAL) :?>
            <?php Modal::begin([
                    'title' => '<h2>Принять задание</h2>',
                    'toggleButton' => [
                        'label' => 'Завершить задание',
                        'tag' => 'button',
                        'class' => 'button button--blue',
                    ],
                    'footer' => $task->name,
                ]);
            ?>
            <?php $form = ActiveForm::begin(['id' => 'modal-form']); ?>
                <?= $form->field($review, 'comment')->textarea(['autofocus' => true]) ?>
                <?= $form->field($review, 'rating')->input('number', ['min' => '0', 'max' => '5']) ?>
                <div class="form-group">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Вернуться</button>
                    <button type="submit" class="btn btn-primary" form="modal-form"
                        name="review" value="ok">Завершить</button>
                </div>
            <?php ActiveForm::end(); ?>
            <?php Modal::end(); ?>
        <?php endif;?>
    <?php endif;?>
    <?php if ($geocode != null) :?>
        <div class="task-map">
            <div 
                id="yandexmap" style="width: 600px; height: 400px">
            </div>
            <p class="map-address town"><?=Html::encode($task->city)?></p>
            <p class="map-address"><?=Html::encode($task->street)?></p>
        </div>
    <?php endif;?>
    <!-- Отклики показываем только для заказчиков или если задание новое -->
    <?php if ($user->id === $task->custom_id or $task->status === Task::STATUS_NEW) :?>
        <h4 class="head-regular">Отклики на задание</h4>
        <?php foreach ($replies as $reply) : ?>
            <div class="response-card">
                <img class="customer-photo" src=<?=Url::to(Html::encode($reply->avatar), true);?>
                    width="146" height="156" alt="Фото заказчиков">
                <div class="feedback-wrapper">
                    <a href="<?='/user/' . $reply->contr_id?>" class="link link--block link--big">
                        <?=$reply->contractor?>
                    </a>
                    <div class="response-wrapper">
                        <div class="stars-rating small">
                            <?php foreach ($reply->rating as $value) :?>
                                <?= $value ? '<span class="fill-star">&nbsp;</span>' :
                                    '<span>&nbsp;</span>'?>
                            <?php endforeach; ?>
                        </div>
                        <p class="reviews">
                            <?=$reply->reviews . ' ' .
                            Yii::$app->helpers->
                            getNounPluralForm(
                                $reply->reviews,
                                'отзыв',
                                'отзыва',
                                'отзывов'
                            );?></p>
                    </div>
                    <p class="response-message">
                        <?=$reply->comment?>
                    </p>
                </div>
                <div class="feedback-wrapper">
                    <p class="info-text"><span class="current-time">
                        <?=Yii::$app->helpers->getTimeStr(Html::encode($reply->add_date));?></span>
                    </p>
                    <p class="price price--small"><?=$reply->price . ' ₽';?></p>
                </div>
                <!-- если активный пользователь - заказчик, рисуем для него кнопки -->
                <?php if ($task->custom_id === $user->id and $reply->status === Reply::STATUS_PROPOSAL) :?>
                    <div class="button-popup">
                        <a href=<?='/accept/' . $reply->id?> class="button blue-button">Принять</a>
                        <a href=<?='/reject/' . $reply->id?> class="button orange-button">Отказать</a>
                    </div>
                <?php endif;?>
            </div>
        <?php endforeach;?> <!-- цикл по откликам на задание -->
    <?php endif;?> <!--  показ откликов на задание -->
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
                <a href=<?='/download/' . $doc->id;?>
                    class="link link--block link--clip">
                    <?=Yii::$app->helpers->shortenFileName($doc->doc);?></a>
                <p class="file-size"><?=$doc->size?> Кб</p>
            </li>
            <?php endforeach;?>
        </ul>
    </div>
</div>

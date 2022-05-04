<?php

/* @var $this yii\web\View */
/* @var $model TasksSelector информация о задании*/
/* @var $categories string array перечень категорий*/

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>
<head>
<script type="text/javascript">
    ymaps.ready(init);
    function init() {
            // Подключаем поисковые подсказки Yandex Map к полю ввода.
            var suggestView = new ymaps.SuggestView('tasksselector-address');
        };
</script>
</head>
<style>
.half-wrapper .form-group input[type=text] {
  width: 330px; 
  padding-right: 1px;  }
.half-wrapper .form-group input[type=date] {
  padding: 0px; }
</style>

<div class="add-task-form regular-form">
    <?php $form = ActiveForm::begin(
        [
            'id' => 'addtask-form',
            'options' => ['class' => 'add-task-form'],
            'action' => Url::to(['tasks/add-task']),
            'method' => 'post',
        ]
    );?>
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
            )->input('text')->hint('Опишите суть работы')->label('Мне нужно');?>
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
                    'address',
                    [
                        'labelOptions' => [
                            'class' => 'control-label',
                        ]
                    ]
                )->input('text')->hint('Укажите адрес задания')->label('Адрес');
                echo $form->field(
                    $model,
                    'city',
                    [
                        'labelOptions' => [
                            'hidden' => 'hidden',
                        ],
                    ]
                )->hiddenInput();
                echo $form->field(
                    $model,
                    'street',
                    [
                        'labelOptions' => [
                            'hidden' => 'hidden',
                        ],
                    ]
                )->hiddenInput();
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
            <div class="task-map">
                <div hidden id="yandexmap" style="width: 300px; height: 200px; background-color: #b8dbff;
                    position: absolute; left: -10px; top: -90px; z-index: 10;">
                </div>
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
<?php
$js = <<<JS
var address = $('#tasksselector-address'),
    city = $('#tasksselector-city'),
    street = $('#tasksselector-street'),
    longitude = $('#tasksselector-longitude'),
    latitude = $('#tasksselector-latitude'),
    mapContainer = $('#yandexmap');
var myMap = null,
    placeMark = null;

address.on('change', function() {
    setTimeout(function() {
        // Забираем запрос из поля ввода.
        var request = address.val();
        // Геокодируем введённые данные.
        ymaps.geocode(request).then(
            function (res) {
                var obj = res.geoObjects.get(0);
                var bounds = obj.properties.get('boundedBy'),
                    // Рассчитываем видимую область для текущего положения пользователя.
                    mapState = ymaps.util.bounds.getCenterAndZoom(
                        bounds, 
                        [mapContainer.width(), mapContainer.height()]
                    );
                    if (address.val().indexOf('Москва') !== -1) {
                        city.val('Москва');
                    } else {
                        city.val(obj.getLocalities()[0]);
                    }
                    street.val(obj.getThoroughfare());
                    latitude.val(mapState.center[0]);
                    longitude.val(mapState.center[1]);
                    longitude.triggerHandler('change');
            },
            function (e) {
            console.log(e)
        });
    }, 400);
});

longitude.on('change', function() {
    var request = address.val();
        // Геокодируем введённые данные.
        ymaps.geocode(request).then(
            function (res) {
                var obj = res.geoObjects.get(0);
                showMap(obj);
            }, 
            function (e) {
            console.log(e)
        });
});

function showMap(obj) {
    mapContainer.removeAttr('hidden');
    var bounds = obj.properties.get('boundedBy'),
        // Рассчитываем видимую область для текущего положения пользователя.
        mapState = ymaps.util.bounds.getCenterAndZoom(
            bounds,
            [mapContainer.width(), mapContainer.height()],
        );
        mapState.controls = [];
        mapState.zoom = 13;
    // Создаём карту.
    createMap(mapState);
}

function createMap(state) {
    if (!myMap) {
        myMap = new ymaps.Map("yandexmap", state);
        placeMark = new ymaps.Placemark(
            myMap.getCenter(),
            {
                iconCaption: 'Место задания',
                balloonContent: 'Место задания'
            }, 
            {
                preset: 'islands#redDotIconWithCaption'
            });
        myMap.geoObjects.add(placeMark);
    } else {
        myMap.setCenter(state.center, state.zoom);
        placeMark.geometry.setCoordinates(state.center);
    }
}
JS;
$this->registerJs($js);
?>

<?php

/* @var $this yii\web\View */
/* @var $model регистрационная информация */
/* @var $cities перечень городов*/

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;

?>

<head>
<script type="text/javascript">
    ymaps.ready(init);
    function init() {
            // Подключаем поисковые подсказки к полю ввода.
            var suggestView = new ymaps.SuggestView('registration-city_name');
        };
</script>
</head>
<style>
.registration-form .form-group input[type=text] {
  width: 300px; }
</style>

<div class="center-block container--registration">
    <div class="regular-form">
        <?php $form = ActiveForm::begin([
            'id' => 'registration-form',
            'options' => ['class' => 'registration-form'],
            'action' => Url::to(['auth/registration']),
            'method' => 'post',
            ]); ?>
            <h3 class="head-main head-task">Регистрация нового пользователя</h3>
            <div class="form-group">
                <?php
                echo $form->field(
                    $model,
                    'name',
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
                    echo $form->field(
                        $model,
                        'email',
                        [
                            'labelOptions' => [
                                'class' => 'control-label',
                            ],
                        ]
                    )->input('email')->hint('Введите эл.адрес')->label('Электронная почта');
                    ?>
                </div>
                <div class="form-group">
                    <?php
                    echo $form->field(
                        $model,
                        'city_name',
                        [
                            'labelOptions' => [
                                'class' => 'control-label',
                            ],
                        ]
                    )->input('text')->hint('Введите адрес')->label('Ваш город');
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
                    )->hiddenInput();
                    echo $form->field(
                        $model,
                        'gorod',
                        [
                            'labelOptions' => [
                                'hidden' => 'hidden',
                            ],
                        ]
                    )->hiddenInput();
                    ?>
                </div>
            </div>
            <div class="form-group">
                <?php
                    echo $form->field(
                        $model,
                        'password_repeat',
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
                    echo $form->field(
                        $model,
                        'password',
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

<?php
$js = <<<JS
var inputCity = $('#registration-city_name'),
    latitude = $('#registration-latitude'),
    longitude = $('#registration-longitude');
inputCity.on('change', function() {
    setTimeout(function() {
        // Забираем запрос из поля ввода.
        var request = inputCity.val();
        // Геокодируем введённые данные.
        ymaps.geocode(request).then(
            function (res) {
                var obj = res.geoObjects.get(0);
                var bounds = obj.properties.get('boundedBy'),
                    // Рассчитываем видимую область для текущего положения пользователя.
                    mapState = ymaps.util.bounds.getCenterAndZoom(
                        bounds, 
                        [600, 400]
                    );
                    latitude.val(mapState.center[0]);
                    longitude.val(mapState.center[1]);
                    //какая-то странность с Москвой. С другими городами такого нет
                    if (inputCity.val().indexOf('Москва') !== -1) {
                        $('#registration-gorod').val('Москва');
                    } else {
                        $('#registration-gorod').val(obj.getLocalities()[0]);
                    }
            }, 
            function (e) {
            console.log(e)
        });
    }, 400);
});
JS;
$this->registerJs($js);
?>

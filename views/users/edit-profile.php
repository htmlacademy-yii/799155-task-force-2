<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Modal;
use yii\widgets\MaskedInput;
use app\models\Categories;
use app\models\ProfileFile;

$user = Yii::$app->helpers->checkAuthorization();

?>
<head>
<script type="text/javascript">
    ymaps.ready(init);
    function init() {
            // Подключаем поисковые подсказки к полю ввода.
            var suggestView = new ymaps.SuggestView('profiledata-address');
        };
</script>
<style>
.regular-form {
  width: 600px;
}
</style>
</head>

<div class="my-profile-form">
    <h3 class="head-main head-regular">Мой профиль</h3>
    <div class="photo-editing">
        <div class="form-group">
            <p class="form-label">Аватар</p>
            <?php if ($model->avatar !== null) :?>
                <img src=<?=$model->avatar?> width="83" height="83">
            <?php else :?>
                <img src=<?=ProfileFile::AVATAR_ANONIM?> width="83" height="83">
            <?php endif;?>
        </div>
    </div>
    <?php Modal::begin([
                'title' => '<h2>Замена автара</h2>',
                'toggleButton' => [
                    'label' => 'Сменить аватар',
                    'tag' => 'button',
                    'class' => 'button button--blue',
                ],
                'footer' => $user->name,
    ]);?>
    <?php $modal = ActiveForm::begin(
        ['id' => 'modal-form',],
        ['options' => ['enctype' => 'multipart/form-data']]
    );?>
        <img src=<?=$model->avatar?> width="83" height="83">
            <?= $modal->field(
                $avatar,
                'file',
                [
                    'labelOptions' => [
                        'class' => 'control-label',
                        'label' => 'Сменить вавтвр',
                    ],
                ]
            )->fileInput(['accept' => 'image/*']);?>
            <div class="control-label">
                <button type="button" class="modal-button" data-dismiss="modal">Отменить</button>
                <button type="submit" class="modal-button" name="modal" value="file">Принять</button>
            </div>
    <?php ActiveForm::end(); ?>
    <?php Modal::end(); ?>
</div>
<div class="regular-form my-profile-form">
    <?php $form = ActiveForm::begin(['id' => 'my-profile-form',]);?>
        <div class="form-group">
            <?php echo $form->field(
                $model,
                'name',
                [
                    'labelOptions' => [
                        'class' => 'form-label',
                    ],
                ]
            )->input('text')->label('Ваше имя');?>
        </div>
        <div class="form-group">
            <?php echo $form->field(
                $model,
                'address',
                [
                    'labelOptions' => [
                        'class' => 'form-label',
                    ],
                ]
                )->input('text')->label('Ваш адрес');
                echo $form->field(
                    $model,
                    'town',
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
                )->hiddenInput();
            ?>
        </div>
        <div class="half-wrapper">
            <div class="form-group">
                <?php echo $form->field(
                    $model,
                    'email',
                    [
                        'labelOptions' => [
                            'class' => 'form-label',
                        ],
                    ]
                )->input('email')->label('Электронная почта');?>
            </div>
            <div class="form-group">
                <?php echo $form->field(
                    $model,
                    'born_date',
                    [
                        'labelOptions' => [
                            'class' => 'form-label',
                        ],
                    ]
                )->input('date')->label('Дата рождения');?>
            </div>
        </div>
        <div class="half-wrapper">
            <div class="form-group">
                <?php echo $form->field($model, 'phone')->
                    label('Укажите номер телефона')->widget(
                        MaskedInput::className(),
                        [
                            'mask' => '+7 (999) 999 99 99',
                        ]
                    )->textInput(['placeholder' => 'Укажите номер телефона']);?>
            </div>
            <div class="form-group">
                <?php echo $form->field(
                    $model,
                    'messenger',
                    [
                        'labelOptions' => [
                            'class' => 'form-label',
                        ],
                    ]
                )->input('text')->label('Ваш Мессенджер');?>
            </div>
        </div>
        <div class="form-group">
            <?php echo $form->field(
                $model,
                'about_info',
                [
                    'labelOptions' => [
                        'class' => 'form-label',
                    ],
                ]
            )->textarea()->label('Информация о себе');?>
        </div>
        <?php if ($model->contractor) :?>
        <div class="form-group">
            <p class="form-label">Выбор специализаций</p>
            <div>
                <?php
                    $this->params = is_array($model->categoriesCheckArray) ?
                        $model->categoriesCheckArray : [];
                    echo $form->field(
                        $model,
                        'categoriesCheckArray',
                        [
                            'labelOptions' => [
                            'class' => 'form-label',
                            'hidden' => 'hidden',
                            ],
                        ]
                    )->checkboxList(
                        $catNames,
                        [
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
                    );?>
            </div>
        </div>
        <?php endif;?>
        <div class="left-column">
            <?= Html::submitButton(
                'Сохранить',
                [
                    'class' => 'button button--blue',
                    'form' => 'my-profile-form',
                    'name' => 'form',
                    'value' => 'save',
                ]
            );?>
        </div>
    <?php ActiveForm::end(); ?>
</div>
<?php
$js = <<<JS
var address = $('#profiledata-address'),
    city = $('#profiledata-town');
    address.on('change', function() {
    setTimeout(function() {
        // Забираем запрос из поля ввода.
        var request = address.val();
        // Геокодируем введённые данные.
        ymaps.geocode(request).then(
            function (res) {
                var obj = res.geoObjects.get(0);
                if (address.val().indexOf('Москва') !== -1) {
                    city.val('Москва');
                } else {
                    city.val(obj.getLocalities()[0]);
                }
                var bounds = obj.properties.get('boundedBy'),
                    mapState = ymaps.util.bounds.getCenterAndZoom(
                        bounds, 
                        [300, 200]
                    );
                    latitude.val(mapState.center[0]);
                    longitude.val(mapState.center[1]);
            },
            function (e) {
            console.log(e)
        });
    }, 400);
});
JS;
$this->registerJs($js);
?>

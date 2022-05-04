<?php

/* @var $this \yii\web\View */
/* @var $model ProfileData */
/* @var $catName array of category names*/
/* @var $avatar ProfileFile */
/* @var $result int управляет показом результата операции*/

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
<?= Html::csrfMetaTags() ?>
<script type="text/javascript">
    ymaps.ready(init);
    function init() {
            // Подключаем поисковые подсказки к полю ввода.
            var suggestView = new ymaps.SuggestView('profiledata-address');
        };
</script>
<style>
.regular-form {
  width: 640px;
}
.my-profile-form {
  margin-left: 0;
}
.my-profile-form .half-wrapper .address input[type=text] {
    width:540px;
    margin-bottom:-40px;
}
.my-profile-form input[type=date],
.my-profile-form .half-wrapper input[type=text] {
  width: 220px;
}
.my-profile-form .form-group input[type=text] {
  width: 260px;
}
.button--black {
    margin-bottom: 15px;
}
.button--black:hover {
    color: #ffffff;
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
                    'class' => 'button button--black',
                ],
                'footer' => $user->name,
    ]);?>
        <?php $modal = ActiveForm::begin(
            ['id' => 'modal-form',],
            ['options' => ['enctype' => 'multipart/form-data']]
        );?>
        <?php if ($model->avatar !== null) :?>
            <img src=<?=$model->avatar?> width="83" height="83">
        <?php else :?>
            <img src=<?=ProfileFile::AVATAR_ANONIM?> width="83" height="83">
        <?php endif;?>
            <?= $modal->field(
                $avatar,
                'file',
                [
                    'labelOptions' => [
                        'class' => 'control-label',
                        'label' => 'Сменить аватар',
                    ],
                ]
            )->fileInput(['accept' => 'image/*']);?>
        <div class="control-label">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Отменить</button>
            <button type="submit" class="btn btn-primary" name="modal" value="file">Принять</button>
        </div>
        <?php ActiveForm::end(); ?>
    <?php Modal::end(); ?>
    <div>
    <?= Html::a(
        'Заменить пароль',
        ['/change-password/' . $user->id,],
        ['class' => 'button button--black', 'style' => 'text-decoration:none']
    );?>
    </div>
</div>
<!-- Modal -->
<div class="overlay" id="overlay"></div>
<div class="modal" tabindex="-1" id="modal" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="label">Профиль изменён</h3>
                <div><input type="hidden" id="result" value="<?=$result?>"></input></div>
            </div>
        </div>
    </div>
</div>
<!--Modal -->
<div class="regular-form my-profile-form">
    <?php $form = ActiveForm::begin(['id' => 'my-profile-form',]);?>
        <div>
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
        <div  class="half-wrapper">
            <div class="address">
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
        </div>
        <div class="half-wrapper">
            <div>
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
            <div>
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
                )->input('text')->label('Ваш Telegram');?>
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
        <div class="form-group">
            <?php
                $params = [
                    'label' => 'Показывать мои контакты только заказчику',
                    'uncheck' => false,
                    //template уехал в params из-за yii\bootstrap4\ActiveForm
                    'template' => '{input}<label class="control-label"
                        for="profiledata-customer_only">{label}</label>',
                ];
                echo $form->field(
                    $model,
                    'customer_only',
                    [
                        'labelOptions' => [
                            'class' => 'form-label',
                        ],
                    ]
                )->checkbox($params, false);
            ?>
        </div>
        <?php endif;?> <!--if ($model->contractor)-->
        <div class="left-column">
            <?= Html::submitButton(
                'Сохранить',
                [
                    'class' => 'button button--black',
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
    city = $('#profiledata-town'),
    longitude = $('#profiledata-longitude'),
    latitude = $('#profiledata-latitude');
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
$(document).ready(function(){
    var modal = $('#modal');
    var overlay = $('#overlay');
    if ($('#result').val() === '1') {
        overlay.fadeIn();
        modal.fadeIn(500);
        setTimeout(function () {
            modal.fadeOut(3000);
            overlay.fadeOut();
            $('#result').val(0);
        }, 3000);
    }
});
JS;
$this->registerJs($js);
?>

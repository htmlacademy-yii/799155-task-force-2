<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\AppAsset;
use app\models\User;

AppAsset::register($this);
$urls = [
    '/site',
    '/site/index',
    '/registration',
    '/logon',
];
$urlAdd = '/add-task';
$hideAddTaskItem = '';
$classMod = '';
if (Url::current() === $urlAdd) {
    $hideAddTaskItem = 'hidden';
    $classMod = 'main-content--center';
}
$hidden = 'hidden';
if (array_search(Url::current(), $urls) === false) {
    $hidden = '';
}
$userName = 'Аноним';
$user = Yii::$app->helpers->checkAuthorization();
if ($user) {
    $userName = $user->name;
}
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
  <?php $this->beginBody() ?>
    <header class="page-header" <?=$hidden?>>
        <nav class="main-nav">
            <a href='/site' class="header-logo">
                <img class="logo-image" src=<?=Url::to('/img/logotype.png', true);?>
                    width=227 height=60 alt="taskforce">
            </a>
            <div class="nav-wrapper">
                <ul class="nav-list">
                    <li class="list-item list-item--active">
                        <a class="link link--nav">Новое</a>
                    </li>
                    <li class="list-item">
                        <a href="#" class="link link--nav">Мои задания</a>
                    </li>
                    <li class="list-item" <?=$hideAddTaskItem?>>
                        <a href="/add-task" class="link link--nav">Создать задание</a>
                    </li>
                    <li class="list-item">
                        <a href="#" class="link link--nav">Настройки</a>
                    </li>
                    <li class="list-item">
                        <a href="/site" class="link link--nav">Главная страница</a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="user-block">
            <a href="#">
                <img class="user-photo" src=<?=Url::to('/img/man-glasses.png', true);?>
                    width="55" height="55" alt="Аватар">
            </a>
            <div class="user-menu">
                <p class="user-name"><?=$userName?></p>
                <div class="popup-head">
                    <ul class="popup-menu">
                        <li class="menu-item">
                            <a href="#" class="link">Настройки</a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="link">Связаться с нами</a>
                        </li>
                        <li class="menu-item">
                            <a href="/logout" class="link">Выход из системы</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <main class="main-content container <?=$classMod?>">
        <?=$content; ?>
    </main>
    <?php if (Url::current() !== $urls[1] and Url::current() !== $urls[0]) :?>
        <footer class="footer-task">
            <div class="container">
                <p class="text-info">&copy; My Company <?= date('Y') ?></p>
                <p class="text-info"><?= Yii::powered() ?></p>
            </div>
        </footer>
    <?php endif; ?>
  <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

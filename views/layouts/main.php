<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\helpers\Url;
use app\assets\AppAsset;
use app\models\User;
use app\models\Profile;
use app\models\ProfileFile;
use app\models\Task;
use app\widgets\Alert;

AppAsset::register($this);
//страницы, у которых не надо показывать заголовок с меню
$urls = [
    '/site',
    '/site/index',
    '/registration',
    '/logon',
    '/contact',
];

//страницы, в верстке которых есть модальные окна
$urlsWhithModal = [
    '/edit-profile/',
    '/task/'
];

$urlAdd = '/add-task';
$hideAddTaskItem = '';
$classMod = '';
if (Url::current() === $urlAdd) {
    $hideAddTaskItem = 'hidden';
    $classMod = 'main-content--center';
}
$hidden = 'hidden';
$res = array_reduce($urls, function ($out, $url) {
    return $out += strstr(Url::current(), $url) === false ? 0 : 1;
}, 0);
if ($res === 0) {
    $hidden = '';
}
//наличие модального окна каким-то образом меняет стили в верстке
//поэтому приходится отслеживать наличие на странице модального окна
$modal = array_reduce($urlsWhithModal, function ($out, $url) {
    return $out += strstr(Url::current(), $url) === false ? 0 : 1;
}, 0);

$userName = 'Аноним';
$avatar = ProfileFile::AVATAR_ANONIM;
$user = Yii::$app->helpers->checkAuthorization();
if ($user) {
    $userName = $user->name;
    $profile = Profile::findOne(['user_id' => $user->id]);
    if ($profile) {
        $avatar = $profile->avatar;
    }
    if ($avatar === null) {
        $avatar = ProfileFile::AVATAR_ANONIM;
    }
}

?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <?php $this->head() ?>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <script src=<?="https://api-maps.yandex.ru/2.1/?apikey=" . Yii::$app->params['mapApiKey'] .
        "&lang=ru_RU"?> type="text/javascript"> </script>
</head>
<style>
html, body, #yandexmap {
  width: 100%; height: 100%; padding: 0; margin: 0; }

.page-header {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
      -ms-flex-pack: justify;
          justify-content: space-between;
  height: <?= $modal > 0 ? '80px' : '59px'?>;
  border-bottom: 1px solid #d6d6d6;
  padding: 15px 12px; }

.user-name:hover ~ .popup-head,
.popup-head:hover {
  display: block;
  position: absolute;
  top: 40px;
  right: 10px; }

input[type=date] {
  height: 38px; }

.my-profile-form input[type=checkbox] ~ label {
  font-weight: 400;
  width: 250px;
  margin-top: 5px; }

.side-menu-item {
  width: 160px;
  -ms-flex-item-align: start;
  align-self: flex-start; }

.list-item:hover:not(.list-item--active) .link--nav {
  text-decoration: none;
  color: #05060f; }

.menu-item a {
  text-decoration: none;
  color: #05060f; }
</style>
<body>
  <?php $this->beginBody() ?>
    <?php if ($hidden === '') :?>
    <header class="page-header">
        <nav class="main-nav">
            <a href='/site' class="header-logo">
                <img class="logo-image" src=<?=Url::to('/img/logotype.png', true);?>
                    width=227 height=60 alt="taskforce">
            </a>
            <div class="nav-wrapper">
                <ul class="nav-list">
                    <li class="list-item
                        <?=strstr(Url::current(), '/tasks') ? 'list-item--active' : ''?>">
                        <a href="/tasks/1" class="link link--nav">Новые</a>
                    </li>
                    <?php if ($user) :?>
                    <li class="list-item
                        <?=strstr(Url::current(), '/my-tasks') ? 'list-item--active' : ''?>">
                        <a href=<?='/my-tasks/' . ($user->contractor > 0 ?
                            Task::FILTER_PROCESS : Task::FILTER_NEW) . '&1'?> 
                            class="link link--nav">Мои задания</a>
                    </li>
                    <?php endif;?>
                    <li class="list-item"
                        <?=strstr(Url::current(), '/add-task') ? 'list-item--active' : ''?>
                        <?=$hideAddTaskItem?>>
                        <a href="/add-task" class="link link--nav">Создать задание</a>
                    </li>
                    <li class="list-item">
                        <a href="/site" class="link link--nav">Главная страница</a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="user-block" <?=$hidden?>>
            <a href=<?='/edit-profile/' . $user->id;?>>
                <img class="user-photo" src=<?=Url::to($avatar, true);?>
                    width="55" height="55" alt="Аватар">
            </a>
            <div class="user-menu">
                <p class="user-name"><?=Html::encode($userName)?></p>
                <div class="popup-head">
                    <ul class="popup-menu">
                        <?php if ($user !== null) :?>
                        <li class="menu-item">
                            <a href=<?='/edit-profile/' . Html::encode($user->id);?>
                                class="link">Настройки</a>
                        </li>
                        <?php endif;?>
                        <li class="menu-item">
                            <a href="/contact" class="link">Связаться с нами</a>
                        </li>
                        <li class="menu-item">
                            <a href="/logout" class="link">Выход из системы</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>
    <?php endif;?>
    <main class="main-content container <?=$classMod?>">
        <div>
            <?= Alert::widget(); ?>
        </div>
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

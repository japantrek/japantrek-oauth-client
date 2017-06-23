<?php
include dirname(__DIR__) . '/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use JT\JTOAuth\OAuthConfig;
use JT\JTOAuth\OAuthManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

$request = Request::createFromGlobals();
$request->setSession(new Session(new NativeSessionStorage()));

$settings = parse_ini_file(__DIR__ . '/config.ini');

$config = new OAuthConfig($settings['jt_id'], $settings['jt_secret'], $request->getUriForPath('/check'), explode(' ', trim($settings['jt_scopes'])));
$authManager = new OAuthManager($config, $request);

switch ($request->getPathInfo()) {
    case '/check':
        if ($authManager->processResponse()) {
            $response = new RedirectResponse($request->getUriForPath('/'));
            $response->send();
        } else {
            echo '<h1>Ошибка</h1>', '<p>', $authManager->getLastError(), '</p>';
        }
        break;
    case '/auth':
        $authManager->startAuthorisationProcess();
        break;
    case '/':

        ?>
        <html>
            <head><title>Пример использования JTOAuth</title></head>
            <body>
                <p>Сейчас авторизован: <?php if ($authManager->isAuthorised()): ?><b style="color: green">ДА</b><?php else:?><b style="color: red">НЕТ</b><?php endif ?></p>
                <?php if ($authManager->isAuthorised()): ?>
                    <?php var_dump($authManager->getUser()) ?>
                <?php else: ?>
                    <p><a href="<?php echo $request->getUriForPath('/auth') ?>">Начать авторизацию</a></p>
                <?php endif ?>
            </body>
        </html>
        <?php
        break;
}
<?php

// Parameters from environment variables, for hosting on Railway (or any host without a
// parameters.yml). Loaded after parameters.yml: a variable that is set wins, otherwise the
// parameters.yml value stays, otherwise the default below applies.
//
// Database: MYSQL_URL (or DATABASE_URL), else MYSQLHOST / MYSQLPORT / MYSQLDATABASE /
// MYSQLUSER / MYSQLPASSWORD - the variables Railway's MySQL service provides.
// The container is cached, so clear app/cache after changing any of these.

$env = function ($name) {
    $value = getenv($name);

    return ($value === false || $value === '') ? null : $value;
};

$set = function ($parameter, $value, $default = null) use ($container) {
    if ($value !== null) {
        $container->setParameter($parameter, $value);
    } elseif (!$container->hasParameter($parameter)) {
        $container->setParameter($parameter, $default);
    }
};

$url = $env('MYSQL_URL') ?: $env('DATABASE_URL');
$db = $url ? parse_url($url) : array();

$set('database_driver', null, 'pdo_mysql');
$set('database_host', isset($db['host']) ? $db['host'] : $env('MYSQLHOST'), '127.0.0.1');
$set('database_port', isset($db['port']) ? (string) $db['port'] : $env('MYSQLPORT'));
$set('database_name', isset($db['path']) ? ltrim($db['path'], '/') : $env('MYSQLDATABASE'), 'railway');
$set('database_user', isset($db['user']) ? urldecode($db['user']) : $env('MYSQLUSER'), 'root');
$set('database_password', isset($db['pass']) ? urldecode($db['pass']) : $env('MYSQLPASSWORD'));

// MySQL 8+/9 defaults to ONLY_FULL_GROUP_BY and strict mode, which this app's queries were
// never written for; use the lenient mode the app has always run under on MariaDB.
$set('database_sql_mode', $env('DATABASE_SQL_MODE'), 'NO_ENGINE_SUBSTITUTION');

$set('mailer_transport', $env('MAILER_TRANSPORT'), 'smtp');
$set('mailer_host', $env('MAILER_HOST'), 'smtp.gmail.com');
$set('mailer_user', $env('MAILER_USER'));
$set('mailer_password', $env('MAILER_PASSWORD'));
$set('mailer_encryption', $env('MAILER_ENCRYPTION'), 'ssl');
$set('mailer_port', $env('MAILER_PORT'), 465);
$set('locale', $env('APP_LOCALE'), 'en');
$set('secret', $env('APP_SECRET'), 'ChangeThisTokenOnRailway');
$set('site_title', $env('SITE_TITLE'), 'eRegistry');
$set('facebook_client_id', $env('FACEBOOK_CLIENT_ID'), '');
$set('facebook_client_secret', $env('FACEBOOK_CLIENT_SECRET'), '');
$set('google_client_id', $env('GOOGLE_CLIENT_ID'), '');
$set('google_client_secret', $env('GOOGLE_CLIENT_SECRET'), '');
$set('recaptacha_client_site', $env('RECAPTCHA_SITE_KEY'), '');
$set('recaptacha_server_side', $env('RECAPTCHA_SECRET_KEY'), '');
$set('app_report_url', $env('APP_REPORT_URL'), 'http://localhost:3000');
$set('app_api_key', $env('APP_API_KEY'), '');

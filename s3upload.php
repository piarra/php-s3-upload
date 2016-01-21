<?php

date_default_timezone_set("Asia/Tokyo");
require_once('vendor/autoload.php');

$profile = 'default';
$bucket = '__BUCKET_NAME__';
$region = 'ap-northeast-1';

if (!isset($argv[1])) {
    echo sprintf("usage: php %s targetFile [s3 key]\n", __FILE__);
    echo "%DATE%, %DATETIME% is available as s3 key\n";
    exit;
}

if (isset($argv[2])) {
    $key = $argv[2];
    $key = str_replace('%DATE%', date('Ymd'), $key);
    $key = str_replace('%DATETIME%', date('YmdHis'), $key);
} else {
    $pathinfo = pathinfo($argv[1]);
    $key = $pathinfo['basename'];
}

$path = dirname(__FILE__) . '/credentials.ini';
$provider = Aws\Credentials\CredentialProvider::ini($profile, $path);

$s3 = Aws\S3\S3Client::factory(array(
    'version' => 'latest',
    'credentials' => $provider,
    'region' => $region
));

try {
    $result = $s3->putObject(array(
        'Bucket'     => $bucket,
        'Key'        => $key,
        'SourceFile' => $argv[1]
    ));
    echo "OK:" . $result['ObjectURL'] . "\n";
} catch (Exception $e) {
    echo $e->getMessage() . "\n";
}

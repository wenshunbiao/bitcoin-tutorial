<?php

use Elliptic\EC;

require_once __DIR__ . '/vendor/autoload.php';

/**
 * 比特币私钥的钱包导入格式：WIF（Wallet Import Format）
 */

$privateKey = 'd4b905ec9fb53484ac8d8355ebee5b20373dbeaf40a0e82c8bb870448d66f942';
echo " * private key: $privateKey\n";

$base58 = new \StephenHill\Base58();

/**
 * 非压缩的私钥格式
 * --------------
 * 非压缩的私钥格式是指在32字节的私钥前添加一个0x80字节前缀，得到33字节的数据，对其计算4字节的校验码，附加到最后，一共得到37字节的数据。
 * 计算校验码非常简单，对其进行两次SHA256，取开头4字节作为校验码。
 * 对这37字节的数据进行Base58编码，得到总是以5开头的字符串编码。
 */
$prefix = '80';
$doubleHash = hash('sha256', hash('sha256', hex2bin($prefix . $privateKey), true));
$check = substr($doubleHash, 0, 8);
$wif = $base58->encode(hex2bin($prefix . $privateKey . $check));
echo "\n非压缩的私钥格式\n--------------\n";
echo " * WIF: $wif\n"; // 5KRyJGduLwPY6sv9UZmYtAUKCGPtCtV6C6qpk5kQ7GSQZNN6feV

/**
 * 压缩的私钥格式
 * --------------
 * 压缩的私钥格式会在32字节的私钥前后各添加一个0x80字节前缀和0x01字节后缀，共34字节的数据，对其计算4字节的校验码，附加到最后，一共得到38字节的数据。
 * 校验码的计算方式和上面相同。
 * 对这38字节的数据进行Base58编码，得到总是以K或L开头的字符串编码。
 */
$prefix = '80';
$suffix = '01';
$doubleHash = hash('sha256', hash('sha256', hex2bin($prefix . $privateKey . $suffix), true));
$check = substr($doubleHash, 0, 8);
$wif = $base58->encode(hex2bin($prefix . $privateKey . $suffix . $check));
echo "\n压缩的私钥格式\n--------------\n";
echo " * WIF: $wif\n"; // L4MDVE1t314UGwjaQfddipoZJix11UEyuznVj9xYzf98yUuMB5zc

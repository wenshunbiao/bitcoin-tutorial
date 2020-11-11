<?php

use Elliptic\EC;

require_once __DIR__ . '/vendor/autoload.php';

$privateKey = 'd4b905ec9fb53484ac8d8355ebee5b20373dbeaf40a0e82c8bb870448d66f942';

$ec = new EC('secp256k1');
// Step1. 生成私钥
$kp = $ec->keyFromPrivate($privateKey);
// 需要随机生成公私钥对可采用此方法
// $kp = $ec->genKeyPair($privateKey);
$privateKey = $kp->getPrivate('hex');
echo " * private key: $privateKey\n";

/**
 * Step2. 生成公钥
 * --------------
 * 由于ECC曲线的特点，根据非压缩格式的公钥(x, y)的x实际上也可推算出y，但需要知道y的奇偶性，因此，可以根据(x, y)推算出x'，作为压缩格式的公钥。
 * 压缩格式的公钥实际上只保存x这一个256位整数，但需要根据y的奇偶性在x前面添加02或03前缀，y为偶数时添加02，否则添加03，这样，得到一个1+32=33字节的压缩格式的公钥数据，记作x'。
 * 非压缩格式的公钥目前已很少使用，原因是非压缩格式的公钥签名脚本数据会更长。
 */
$publicKey = $kp->getPublic('hex'); // 非压缩公钥
$publicEncKey = $kp->getPublic(true, 'hex'); // 压缩公钥
echo " * public key: $publicKey\n"; // 04b3a6fd76499bae61b8b422f0ce7452d43975230192235fb86fcdea063782c4e8bcd16d0a9a3ae3770fe9a12cb3ad650ab5da8b8dc40ee5499e58ba3477d21f36
echo " * publicEnc key: $publicEncKey\n"; // 02b3a6fd76499bae61b8b422f0ce7452d43975230192235fb86fcdea063782c4e8

// Step3. 计算公钥的 SHA-256 哈希值（32bytes）
$hash256 = hash('sha256', hex2bin($publicEncKey));
echo " * Step3 hash256: $hash256\n"; // 223446ebf32ffcab4f4fe9c845bf79def5c2a2ba021a0e0f6e18f1f5aab3b9d7

// Step4. 取上一步结果，计算 RIPEMD-160 哈希值(20bytes)：
$hash160 = hash('ripemd160', hex2bin($hash256));
echo " * Step4 hash160: $hash160\n"; // c5ab9fe3f256a1db9028cde6bd9c4a20610d0f97

// Step5. 取上一步结果，前面加入地址版本号（比特币主网版本号 0x00）：
$step5 = '00' . $hash160;
echo " * Step5: $step5\n"; // 00c5ab9fe3f256a1db9028cde6bd9c4a20610d0f97

// Step6. 取上一步结果，计算两次 SHA-256 哈希值(32bytes)
$doubleHash = hash('sha256', hash('sha256', hex2bin($step5), true));
echo " * Step6 double hash: $doubleHash\n"; // 0c6c63165fcb94f7735c98f981afdbc6edde8de887347f5f9d6b5fbf2040f14c

// Step7. 取上一步结果的前4个字节（8位十六进制），把这4个字节加在第5步的结果后面，作为校验（这就是比特币地址的16进制形态）
$checksum = substr($doubleHash, 0, 8);
$hexStr = $step5 . $checksum;
echo " * Step7: $hexStr\n"; // 00c5ab9fe3f256a1db9028cde6bd9c4a20610d0f970b151e05

// Step8. 用base58编码变换一下地址（这就是最常见的比特币地址形态）
$base58 = new \StephenHill\Base58();
$address = $base58->encode(hex2bin($hexStr));
echo " * address: $address\n"; // 1K2BiUYadAzhk8vFaEwXfRGWZo8WabKbkp

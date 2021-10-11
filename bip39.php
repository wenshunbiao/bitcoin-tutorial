<?php

use FurqanSiddiqui\BIP39\BIP39;

require_once __DIR__ . '/vendor/autoload.php';

/**
 * 比特币的【HD钱包】BIP39生成助记词
 */

// 口令
$passphrase = 'bitcoin';
echo " * Passphrase: $passphrase\n";

$mnemonic = BIP39::Generate(12);

// 长度为 128~256 位 (bits) 的随机序列(熵)
echo " * Generate Entropy (128bits): $mnemonic->entropy\n";

// 助记词
echo " * Words: " . implode(' ', $mnemonic->words) . "\n";

// 随机序列一定是11的倍数，平均划分为不同的11位倍数。
echo " * Raw Binary Chunks: " . implode(' ', $mnemonic->rawBinaryChunks) . "\n";

// 助记词转化为 BIP32 种子
echo " * Seed: " . bin2hex($mnemonic->generateSeed($passphrase)) . "\n\n";

// 从助记词推导种子
echo "Reverse (Mnemonic to Entropy):\n";

$mnemonic2 = BIP39::Words($mnemonic->words);

echo " * Words: " . implode(' ', $mnemonic2->words) . "\n";

echo " * Entropy: $mnemonic2->entropy\n";

echo " * Seed: " . bin2hex($mnemonic2->generateSeed($passphrase)) . "\n";

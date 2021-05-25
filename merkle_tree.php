<?php

/**
 * 比特币的Merkle Tree计算
 * 计算block高度123456的Merkle Tree
 * https://btc.com/0000000000002917ed80650c6174aac8dfc46f5fe36480aaef682ff6cd83c3ca
 */

echo " * 计算block高度123456的Merkle Tree\n";
echo " * https://btc.com/0000000000002917ed80650c6174aac8dfc46f5fe36480aaef682ff6cd83c3ca\n";

echo "哈希列表：\n";
$hash_list = [
    '5b75086dafeede555fc8f9a810d8b10df57c46f9f176ccc3dd8d2fa20edd685b',
    'e3d0425ab346dd5b76f44c222a4bb5d16640a4247050ef82462ab17e229c83b4',
    '137d247eca8b99dee58e1e9232014183a5c5a9e338001a0109df32794cdcc92e',
    '5fd167f7b8c417e59106ef5acfe181b09d71b8353a61a55a2f01aa266af5412d',
    '60925f1948b71f429d514ead7ae7391e0edf965bf5a60331398dae24c6964774',
    'd4d5fc1529487527e9873256934dfb1e4cdcb39f4c0509577ca19bfad6c5d28f',
    '7b29d65e5018c56a33652085dbb13f2df39a1a9942bfe1f7e78e97919a6bdea2',
    '0b89e120efd0a4674c127a76ff5f7590ca304e6a064fbc51adffbd7ce3a3deef',
    '603f2044da9656084174cfb5812feaf510f862d3addcf70cacce3dc55dab446e',
    '9a4ed892b43a4df916a7a1213b78e83cd83f5695f635d535c94b2b65ffb144d3',
    'dda726e3dad9504dce5098dfab5064ecd4a7650bfe854bb2606da3152b60e427',
    'e46ea8b4d68719b65ead930f07f1f3804cb3701014f8e6d76c4bdbc390893b94',
    '864a102aeedf53dd9b2baab4eeb898c5083fde6141113e0606b664c41fe15e1f',
];
print_r($hash_list);

$merkle_root = merkle($hash_list);
echo " * 计算得到的 Merkle Root 为：\n";
echo "$merkle_root\n";
// 0e60651a9934e8f0decd1c5fde39309e48fca0cd1c84a21ddfde95033762d86c

/*
|--------------------------------------------------------------------------
| 辅助函数
|--------------------------------------------------------------------------
*/

/**
 * 计算Merkle Tree
 *
 * @param array $hash_list 哈希列表
 * @param bool $is_ntohs 是否进行大小端字节序转换
 * @return string Merkle Root
 */
function merkle(array $hash_list, $is_ntohs = true)
{
    // 大小端字节序转换
    if ($is_ntohs) {
        foreach ($hash_list as $key => $item) {
            $hash_list[$key] = ntohs($item);
        }
    }

    $len = count($hash_list);

    // 返回 Merkle Root
    if ($len == 1) {
        return ntohs($hash_list[0]);
    }

    $new_hash_list = [];
    for ($i = 0; $i < $len - 1; $i += 2) {
        $new_hash_list[] = double_hash256($hash_list[$i], $hash_list[$i + 1]);
    }

    // 奇数个数，最后一个自己与自己配对
    if ($len % 2 == 1) {
        $new_hash_list[] = double_hash256(end($hash_list), end($hash_list));
    }

    return merkle($new_hash_list, false);
}

/**
 * 双重 hash256
 *
 * @param $tx1 string 大小端转换后的数据
 * @param $tx2 string 大小端转换后的数据
 * @return string
 */
function double_hash256($tx1, $tx2)
{
    return hash('sha256', hash('sha256', hex2bin($tx1 . $tx2), true));
}

/**
 * 十六进制字符串 大小端字节序转换
 *
 * @param $hex
 * @return string
 */
function ntohs($hex)
{
    return bin2hex(pack('h*', strrev($hex)));
}

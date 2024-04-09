<?php

namespace App\Services;


class SecuritryUtils
{
    protected String $str = "0123456789";
    protected String $key = "0cc2e31706827e33ddfd21fdcc2a4c73";

    public function __construct(){

    }

    public function encrypt()
    {

        $str = "0123456789";
        $key = "0cc2e31706827e33ddfd21fdcc2a4c73";

        $cipher="AES-256-ECB";
        //OPENSSL_RAW_DATA and OPENSSL_ZERO_PADDING.
        $ciphertext_raw = openssl_encrypt($str, $cipher, $key, OPENSSL_RAW_DATA);
        echo $ciphertext_raw . '<br>';
        echo base64_encode($ciphertext_raw) . '<br>';
        echo bin2hex($ciphertext_raw) . '<br>';


        //$hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary=true);
        //echo $hmac . '<br>';
        //$ciphertext = base64_encode( $hmac.$ciphertext_raw );
        //echo $ciphertext . '<br>';
    }

    function binaryToDecimal($binaryString) {
        $decimal = 0;
        $length = strlen($binaryString);
        for ($i = 0; $i < $length; $i++) {
            $decimal |= ord($binaryString[$i]) << (8 * ($length - $i - 1));
        }
        return $decimal;
    }

    function aesEncrypt2($data, $key) {
        $blockSize = 16; // AES的块大小是128位，即16字节
        $padding = $blockSize - (strlen($data) % $blockSize);
        $data = $data . str_repeat(chr($padding), $padding); // PKCS#7 填充
        //         //  OPENSSL_RAW_DATA
        $encrypted = openssl_encrypt($data, 'AES-256-ECB', $key, OPENSSL_RAW_DATA);
        return $encrypted; // 返回Base64编码的密文
        //return base64_encode($encrypted); // 返回Base64编码的密文
    }

    function aesEncrypt($data, $key) {
        $encrypted = openssl_encrypt($data, 'AES-256-ECB', $key, OPENSSL_RAW_DATA | OPENSSL_NO_PADDING);
        // 使用PKCS7Padding填充
        //$blockSize = 16; // AES的块大小是128位，即16字节
        $blockSize = 32; // AES的块大小是128位，即16字节
        $paddingSize = $blockSize - (strlen($encrypted) % $blockSize);
        $padding = str_repeat(chr($paddingSize), $paddingSize);
        $encrypted .= $padding;
        return $encrypted;
        //return base64_encode($encrypted);
    }

    // 补充函数，PKCS7填充
    function pkcs7_pad($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }
}

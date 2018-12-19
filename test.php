<?php
echo "<pre>";
// echo decbin(11111111);
// echo 15 << 3;
// echo 16 >> 5;
// echo 1 & 1;
// echo 0 | 0;
// echo 1 ^ 1;
// echo ~1;
// 0.0.0.0.0.0.0.0.0.0.0.0.0.0.0.0
// 0.0.0.0.0.0.0.0.1.1.1.1.1.1.1.1

// echo bindec(127);
for ($i=0, $h=$i; $i < 5, $h < 4; $i++, $h++) {
    echo "\n$i . $h";

}

// print_r(pack('C', 65535) );
// echo decbin(5);
// echo 135 & 125;
// echo 65535 & 255;

// function encode($message)
// {
//     $length = strlen($message);
//
//     $bytesHeader = [];
//     $bytesHeader[0] = 129; // 0x1 text frame (FIN + opcode)
//
//     if ($length <= 125) {
//             $bytesHeader[1] = $length;
//     } else if ($length >= 126 && $length <= 65535) {
//             $bytesHeader[1] = 126;
//             $bytesHeader[2] = ( $length >> 8 ) & 255;
//             $bytesHeader[3] = ( $length      ) & 255;
//     } else {
//             $bytesHeader[1] = 127;
//             $bytesHeader[2] = ( $length >> 56 ) & 255;
//             $bytesHeader[3] = ( $length >> 48 ) & 255;
//             $bytesHeader[4] = ( $length >> 40 ) & 255;
//             $bytesHeader[5] = ( $length >> 32 ) & 255;
//             $bytesHeader[6] = ( $length >> 24 ) & 255;
//             $bytesHeader[7] = ( $length >> 16 ) & 255;
//             $bytesHeader[8] = ( $length >>  8 ) & 255;
//             $bytesHeader[9] = ( $length       ) & 255;
//     }
//
//     $str = implode(array_map("chr", $bytesHeader)) . $message;
//
//     return $str;
// }
//
// print_r(encode('abangkau ini'));

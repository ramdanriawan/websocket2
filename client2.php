<?php
echo "<pre>";
set_time_limit(0);

function decodeMsg($encodedMsg){
    // jadikan encodedMsg nya sebagai nomor dari ascii character
    $buffer = array_map('ord', str_split($encodedMsg));

    // ketahui panjang bytes nya
    $length = $buffer[1];

    // ambil posisi masksnya
    if ($length < 254)
        $masksPos = 2;
    else if ($length == 254)
        $masksPos = 4;
    else
    $masksPos = 10;

    // dapatkan masks perbyte nya
    $masks = array_slice($buffer, $masksPos, 4);

    // dapatkan message realnya
    $messagesPos = $masksPos + 4;
    $messagesReal = array_slice($buffer, $messagesPos);

    // xor kan setiap string
    $h = 0;
    for ($i=0; $i < count($messagesReal); $i++) {
        if ($h == 4) $h = 0;

        $messagesReal[$i] = $messagesReal[$i] ^ $masks[$h];

        $h++;
    }

    // jadikan setiap messagesRealnya sebagai string char ascii
    $messagesChr = array_map('chr', $messagesReal);
    $messagesOut = implode($messagesChr);

    // kembalikan messagesnya yang sudah dijadikan string
    return $messagesOut;
};

function encodeMsg($msg){
    // ketahui panjang msg nya
    $msgLen = strlen($msg);

    // set type dari message nya 129 (untuk text frame)
    $msgByte[0] = 129;

    // pecahkan lengthnya menjadi beberapa byte
    if($msgLen <= 125)
        $msgByte[1] = $msgLen;
    else if($msgLen >= 126 && $msgLen <= 65535) {
        $msgByte[1] = 126;
        $msgByte[2] = ( $msgLen >> 8 ) & 255;
        $msgByte[3] = $msgLen & 255;
    }
    else {
        $msgByte[1] = 127;
        $msgByte[2] = ( $msgLen >> 56 ) & 255;
        $msgByte[3] = ( $msgLen >> 48 ) & 255;
        $msgByte[4] = ( $msgLen >> 40 ) & 255;
        $msgByte[5] = ( $msgLen >> 32 ) & 255;
        $msgByte[6] = ( $msgLen >> 24 ) & 255;
        $msgByte[7] = ( $msgLen >> 16 ) & 255;
        $msgByte[8] = ( $msgLen >> 8 )  & 255;
        $msgByte[9] = $msgLen & 255;
    }

    // jadikan setiap length array sebagai char ascii
    $msgChr = array_map('chr', $msgByte);

    // implode dan jadikan strng
    $msgImp = implode($msgChr);

    //lalu gabungkan dengan message aslinya
    $msgOut = $msgImp . $msg;

    // kembalikan nilai msg nya
    return $msgOut;
};

// koneksi socket
if(!$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)){
    $errno = socket_last_error($sock);
    $errMsg = socket_strerror($errno);

    die("\r\nTerjadi kesalahan " . $errMsg);
}

echo "\r\nBerhasil membuat koneksi socket";

// kirimkan pesan
$addr = '192.168.43.205';
$port = 5000;

if(!$client = socket_connect($sock, $addr, $port)){
    $errno = socket_last_error($sock);
    $errMsg = socket_strerror($errno);

    die("\r\nTerjadi kesalahan saat socket connect " . $errMsg);
}

echo "\r\nBerhasil membuat socket connect";

$msg = 'kampret';

socket_write($sock, $msg, strlen($msg));

$response = socket_read($sock, 5000000);
echo "\r\nserver: " . $response . "\r\n";

$msg = '@!kampret';
socket_write($sock, $msg, strlen($msg));
$response = socket_read($sock, 5000000);
echo "\r\n" . $response . "\r\n";

<?php
set_time_limit(0);
// error_reporting(0);

// buat koneksi socket untuk stream dengan tcp
if(!$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)){
    $errno = socket_last_error();
    $errMsg = socket_strerror($errno);

    die($errMsg);
}

echo "\nBerhasil membuat koneksi socket";

// bind socket
$addr = '192.168.43.205';
$port = 5000;

if(! socket_bind($sock, $addr, $port)){
    $errno = socket_last_error();
    $errMsg = socket_strerror($errno);

    die($errMsg);
}

echo "\nBerhasil bind socket";

// setelah dibind saatnya dilisten
if(! socket_listen($sock)){
    $errno = socket_last_error();
    $errMsg = socket_strerror($errno);

    die($errMsg);
}

echo "\nBerhasil listen di $addr:$port";

function showError(){
    $errno = socket_last_error();
    $errMsg = socket_strerror($errno);

    return $errMsg;
}

$clients = array();
$reads = array();

// terima koneksi yang masuk
$client = socket_accept($sock) or die(showError());

// dapatkan identitas yang masuk
socket_getpeername($client, $addr, $port)  or die(showError());
echo "\n$addr:$port connected";

//kirimkan response header
$message = socket_read($client, 2048)  or die(showError());
$requests = preg_split("/\r\n/", $message);
$requestSecWebSocket = null;

foreach($requests as $request){
    $request = explode(': ', $request);

    if($request[0] == 'Sec-WebSocket-Key'){
        $requestSecWebSocket = $request[1];
    }
}

$accept = sha1("{$requestSecWebSocket}258EAFA5-E914-47DA-95CA-C5AB0DC85B11", true);
$accept = base64_encode($accept);
$response = "HTTP/1.1 101 Switching Protocols\r\nUpgrade: WebSocket\r\nSec-WebSocket-Accept: $accept\r\nConnection: Upgrade\r\nSec-WebSocket-Origin: http://localhost\r\n\r\n";

if(! socket_write($client, $response, strlen($response))){
    die(showError());
}

echo "\n$response\ntelah sended";

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

// cek terus pesan yang masuk dari client
while(true){
    echo socket_accept($sock);

    $buf = socket_recv($client, $read, 5000000, 0);

    // spesifikasikan jika pesan yang dikirim adalah dari server atau client
    $bufCek = array_map('ord', str_split($read));
    if($bufCek[0] == 64 && $bufCek[1] == 33){ /* 64 dan 33 adalah kode dari server untuk menandakan pesan bahwa itu dari server @!*/
        if($read != null){
            array_shift($bufCek);
            array_shift($bufCek);

            $msg = implode(array_map('chr', $bufCek));

            echo "\nClient: " . $msg;

            $buf = "Server: " . $msg;

            socket_write($client, $buf, strlen($buf));
        }
    }
    else if($read == null){ /*cek jika client telah disconnected*/
        echo "\n$addr:$port disconnected";
        break;
    }
    else { /*cek jika pesan yang dikirim adlah dari browser*/
        if($read != null){
            $msg = decodeMsg($read);
            echo "\nClient: " . $msg;

            $buf = encodeMsg("Server: kamu mengirimkan pesan " . $msg);

            socket_write($client, $buf, strlen($buf));
        }
    }
}

<?php
// pengaturan server websocket yang akan dibuat
set_time_limit(0);
error_reporting(0);
$maxClients = 2;
$secWebsocketOrigin = 'http://localhost';

// untuk mencek jika terjadi error pada fungsi socket yang sedang digunakan
function showError(){
    $errno = socket_last_error();
    $errMsg = socket_strerror($errno);

    return $errMsg;
}

// untuk mendecode pesan yang diterima dari browser
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

// untuk menencode pesan  yang dikirimkan ke browser
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

// untuk mengirimkan response header koneksi websocket ke client
function sendResponseHeaders($clientNew)
{
    global $secWebsocketOrigin;

    $reqHeaders = socket_read($clientNew, 2048)  or die(showError());
    $reqHeaders = preg_split("/\r\n/", $reqHeaders);
    $reqHeaderSecWebSocket = null;

    foreach($reqHeaders as $reqHeader){
        $reqHeader = explode(': ', $reqHeader);

        if($reqHeader[0] == 'Sec-WebSocket-Key'){
            $reqHeaderSecWebSocket = $reqHeader[1];
        }
    }

    $accept = sha1("{$reqHeaderSecWebSocket}258EAFA5-E914-47DA-95CA-C5AB0DC85B11", true);
    $accept = base64_encode($accept);
    $response = "HTTP/1.1 101 Switching Protocols\r\nUpgrade: WebSocket\r\nSec-WebSocket-Accept: $accept\r\nConnection: Upgrade\r\nSec-WebSocket-Origin: $secWebsocketOrigin\r\n\r\n";

    if(! socket_write($clientNew, $response, strlen($response))){
        die(showError());
    }
};

// buat koneksi socket untuk stream dengan tcp
if(!$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)){

    die(showError());
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

// set nilai clients sebagai array
$clients = [];

// selalu jalankan
while(true)
{
    // set ke nilai kosong
    $reads = [];
    $reads[0] = $sock;

    // isi lagi nilai reads yang telah diubah oleh socket_select
    $reads = array_merge($reads, $clients);

    // watch untuk setiap client yang melakukan perubahan
    if(!socket_select($reads, $write, $except, null)){
        die(showError());
    }

    // cek jika ada client yang mencoba untuk melakukan koneksi dan kirim pesan penuh jika client udah penuh
    if(in_array($sock, $reads)){
        $clientNew = socket_accept($sock);

        if(count($clients) < $maxClients){

            array_push($clients, $clientNew);

            socket_getpeername($clientNew, $addr, $port);

            $clientNewPos = array_search($clientNew, $clients);;

            echo "\n$addr:$port (client ke $clientNewPos) connected";

            //kirimkan response header ke browser
            sendResponseHeaders($clientNew);
        } else {
            // kirimkan dulu response header supaya bisa membuat pemberitahuan ke browser bahwa client penuh
            sendResponseHeaders($clientNew);

            socket_write($clientNew, encodeMsg("server: client sudah penuh silakan coba beberapa saat lagi"));

            socket_close($clientNew);
        }
    }

    // cek setiap incoming message dari client
    foreach($reads as $key => $value){
        if($value != $sock){

            $buf = socket_recv($value, $read, 5000000, 0);

            // spesifikasikan jika pesan yang dikirim adalah dari server atau client
            $bufCek = array_map('ord', str_split($read));
            if($bufCek[0] == 64 && $bufCek[1] == 33){ /* 64 dan 33 adalah kode dari server untuk menandakan pesan bahwa itu dari server @!*/
                if($read != null){
                    array_shift($bufCek);
                    array_shift($bufCek);

                    $msg = implode(array_map('chr', $bufCek));

                    echo "\nClient: " . $msg;

                    $buf = "Server: " . $msg . ' diterima';

                    socket_write($value, $buf, strlen($buf));
                }
            }
            else if($read == null){ /*cek jika client telah disconnected*/
                $clientDiscPos = array_search($value, $clients);
                socket_close($clients[$clientDiscPos]);

                unset($clients[$clientDiscPos]);

                echo "\n$addr:$port disconnected";
            }
            else { /*cek jika pesan yang dikirim adlah dari browser*/
                if($read != null){
                    $msg = decodeMsg($read);
                    echo "\nClient: " . $msg;

                    $buf = encodeMsg("Server: kamu mengirimkan pesan " . $msg);

                    socket_write($value, $buf, strlen($buf));
                }
            }
        }
    }
}

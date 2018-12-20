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
// for ($i=0, $h=$i; $i < 5, $h < 4; $i++, $h++) {
//     echo "\n$i . $h";
//
// }

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

<?php
error_reporting(E_ALL);
set_time_limit(0);
ob_implicit_flush();
$address = ‘172.16.34.170’; //ganti dengan localhost atau ip komputer kamu
$port = 9999; //terserah berapa aja, asal diatas 1024. Karena flash membaca port > 1024
function send_Message($allclient, $socket, $buf) {
foreach($allclient as $client) {
socket_write($client, “$socket wrote: $buf”);
}
}

if (($master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) < 0) {
echo “socket_create() failed, reason: ” . socket_strerror($master) . “n”;
}

socket_set_option($master, SOL_SOCKET,SO_REUSEADDR, 1);
if (($ret = socket_bind($master, $address, $port)) < 0) {
echo “socket_bind() failed, reason: ” . socket_strerror($ret) . “n”;
}
if (($ret = socket_listen($master, 5)) < 0) {
echo “socket_listen() failed, reason: ” . socket_strerror($ret) . “n”;
}
$read_sockets = array($master);
while (true) {
$changed_sockets = $read_sockets;
$num_changed_sockets = socket_select($changed_sockets, $write = NULL, $except = NULL, NULL);
foreach($changed_sockets as $socket) {
if ($socket == $master) {
if (($client = socket_accept($master)) < 0) {
echo “socket_accept() failed: reason: ” . socket_strerror($msgsock) . “n”;
continue;
} else {
array_push($read_sockets, $client);
}
} else {
$bytes = socket_recv($socket, $buffer, 2048, 0);
if ($bytes == 0) {
$index = array_search($socket, $read_sockets);
unset($read_sockets[$index]);
socket_close($socket);
}else{
$allclients = $read_sockets;
array_shift($allclients);
send_Message($allclients, $socket, $buffer);
}
}

}
}
?>

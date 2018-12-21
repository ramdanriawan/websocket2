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

// <?php
// error_reporting(E_ALL);
// set_time_limit(0);
// ob_implicit_flush();
// $address = ‘172.16.34.170’; //ganti dengan localhost atau ip komputer kamu
// $port = 9999; //terserah berapa aja, asal diatas 1024. Karena flash membaca port > 1024
// function send_Message($allclient, $socket, $buf) {
// foreach($allclient as $client) {
// socket_write($client, “$socket wrote: $buf”);
// }
// }
//
// if (($master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) < 0) {
// echo “socket_create() failed, reason: ” . socket_strerror($master) . “n”;
// }
//
// socket_set_option($master, SOL_SOCKET,SO_REUSEADDR, 1);
// if (($ret = socket_bind($master, $address, $port)) < 0) {
// echo “socket_bind() failed, reason: ” . socket_strerror($ret) . “n”;
// }
// if (($ret = socket_listen($master, 5)) < 0) {
// echo “socket_listen() failed, reason: ” . socket_strerror($ret) . “n”;
// }
// $read_sockets = array($master);
// while (true) {
// $changed_sockets = $read_sockets;
// $num_changed_sockets = socket_select($changed_sockets, $write = NULL, $except = NULL, NULL);
// foreach($changed_sockets as $socket) {
// if ($socket == $master) {
// if (($client = socket_accept($master)) < 0) {
// echo “socket_accept() failed: reason: ” . socket_strerror($msgsock) . “n”;
// continue;
// } else {
// array_push($read_sockets, $client);
// }
// } else {
// $bytes = socket_recv($socket, $buffer, 2048, 0);
// if ($bytes == 0) {
// $index = array_search($socket, $read_sockets);
// unset($read_sockets[$index]);
// socket_close($socket);
// }else{
// $allclients = $read_sockets;
// array_shift($allclients);
// send_Message($allclients, $socket, $buffer);
// }
// }
//
// }
// }

// error_reporting(~E_NOTICE);
// set_time_limit (0);
//
// $address = "0.0.0.0";
// $port = 5000;
// $max_clients = 10;
//
// if(!($sock = socket_create(AF_INET, SOCK_STREAM, 0)))
// {
// 	$errorcode = socket_last_error();
//     $errormsg = socket_strerror($errorcode);
//
//     die("Couldn't create socket: [$errorcode] $errormsg \n");
// }
//
// echo "Socket created \n";
//
// // Bind the source address
// if( !socket_bind($sock, $address , 5000) )
// {
// 	$errorcode = socket_last_error();
//     $errormsg = socket_strerror($errorcode);
//
//     die("Could not bind socket : [$errorcode] $errormsg \n");
// }
//
// echo "Socket bind OK \n";
//
// if(!socket_listen ($sock , 10))
// {
// 	$errorcode = socket_last_error();
//     $errormsg = socket_strerror($errorcode);
//
//     die("Could not listen on socket : [$errorcode] $errormsg \n");
// }
//
// echo "Socket listen OK \n";
//
// echo "Waiting for incoming connections... \n";
//
// //array of client sockets
// $client_socks = array();
//
// //array of sockets to read
// $read = array();
//
// //start loop to listen for incoming connections and process existing connections
// while (true)
// {
// 	//prepare array of readable client sockets
// 	$read = array();
//
// 	//first socket is the master socket
// 	$read[0] = $sock;
//
// 	//now add the existing client sockets
//     for ($i = 0; $i < $max_clients; $i++)
//     {
//         if($client_socks[$i] != null)
// 		{
// 			$read[$i+1] = $client_socks[$i];
// 		}
//     }
//
// 	//now call select - blocking call
//     if(socket_select($read , $write , $except , null) === false)
// 	{
// 		$errorcode = socket_last_error();
// 		$errormsg = socket_strerror($errorcode);
//
// 		die("Could not listen on socket : [$errorcode] $errormsg \n");
// 	}
//
//     //if ready contains the master socket, then a new connection has come in
//     if (in_array($sock, $read))
// 	{
//         for ($i = 0; $i < $max_clients; $i++)
//         {
//             if ($client_socks[$i] == null)
// 			{
//                 $client_socks[$i] = socket_accept($sock);
//
//                 //display information about the client who is connected
// 				if(socket_getpeername($client_socks[$i], $address, $port))
// 				{
// 					echo "Client $address : $port is now connected to us. \n";
// 				}
//
// 				//Send Welcome message to client
// 				$message = "Welcome to php socket server version 1.0 \n";
// 				$message .= "Enter a message and press enter, and i shall reply back \n";
// 				socket_write($client_socks[$i] , $message);
// 				break;
//             }
//         }
//     }
//
//     //check each client if they send any data
//     for ($i = 0; $i < $max_clients; $i++)
//     {
// 		if (in_array($client_socks[$i] , $read))
// 		{
// 			$input = socket_read($client_socks[$i] , 1024);
//
//             if ($input == null)
// 			{
// 				//zero length string meaning disconnected, remove and close the socket
// 				unset($client_socks[$i]);
// 				socket_close($client_socks[$i]);
//             }
//
//             $n = trim($input);
//
//             $output = "OK ... $input";
//
// 			echo "Sending output to client \n";
//
// 			//send response to client
// 			socket_write($client_socks[$i] , $output);
// 		}
//     }
// }

$arrays[4] = 'mugu';
$arrays[5] = 'mugi';
$arrays[6] = 'muga';

unset($arrays[5]);

print_r($arrays);
foreach ($arrays as $key => $value) {
    echo $key . ' ' . $value;
}
?>

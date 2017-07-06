<?php
	require 'vendor/autoload.php';

	use React\Socket\ConnectionInterface;

	$loop = React\EventLoop\Factory::create();

	$socket = new React\Socket\Server('127.0.0.1:8080', $loop);

	$socket->on('connection', function (ConnectionInterface $connection){
	   $connection->write('Selam');
	   $connection->on('data', function ($data) use ($connection){
	      $connection->write($data . ", šta?");
       });
    });


	echo "Listening on" . $socket->getAddress() . "\n";
	$loop->run();



?>
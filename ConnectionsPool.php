<?php

    require __DIR__.'/vendor/autoload.php';

    use React\Socket\ConnectionInterface;

    class ConnectionsPool{
        protected $connections;

        public function __construct()
        {
            $this->connections = new SplObjectStorage();
        }

        public function add(ConnectionInterface $connection){
            $connection->write("Selam\n");

            $this->initEvents($connection);
            $this->connections->attach($connection);

            $this->sendAll("Novi korisnik je ušao u chat\n", $connection);
        }

        protected function initEvents(ConnectionInterface $connection){
            $connection->on('data', function($data) use ($connection){
                $this->sendAll($data, $connection);
            });

            $connection->on('close', function() use ($connection){
                $this->connections->detach($connection);
                $this->sendAll("Korisnik je napustio chat\n", $connection);
            });
        }

        protected function sendAll($text, ConnectionInterface $connection){
            foreach ($this->connections as $conn) {
                if($conn !== $connection) $conn->write($text);
            }

        }

    }
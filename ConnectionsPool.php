<?php

    require __DIR__.'/vendor/autoload.php';

    use React\Socket\ConnectionInterface;

    class ConnectionsPool{
        protected $connections;

        public function __construct()
        {
            $this->connections = new SplObjectStorage();
        }

        protected function setConnectionData(ConnectionInterface $connection, $data){
            $this->connections->offsetSet($connection, $data);
        }

        protected function getConnectionData(ConnectionInterface $connection){
            $this->connections->offsetGet($connection);
        }

        public function add(ConnectionInterface $connection){
            $connection->write("Unesi korisničko ime: ");

            $this->initEvents($connection);
            $this->setConnectionData($connection, []);
            $this->connections->attach($connection);

            $this->sendAll("Novi korisnik je ušao u chat\n", $connection);
        }

        protected function initEvents(ConnectionInterface $connection){
            $connection->on('data', function($data) use ($connection){
                $connectionData = $this->getConnectionData($connection);

                if(empty($connectionData)){
                    $this->sendJoinMessage($data, $connection);
                    return;
                }

                $name = $connectionData['name'];
                $this->sendAll("$name: $data", $connection);
            });

            $connection->on('close', function() use ($connection){
                $connectionData = $this->getConnectionData($connection);
                $name = $connectionData['name'];
                $this->connections->offsetUnset($connection);
                $this->sendAll("Korisnik $name je napustio chat\n", $connection);
            });
        }

        protected function sendJoinMessage($name, $connection){
            $name = str_replace(['\n', '\r'], " ", $name);
            $this->setConnectionData($connection, ['name' => $name]);
            $this->sendAll("Korisnik $name je ušao u chat\n", $connection);
        }

        protected function sendAll($text, ConnectionInterface $connection){
            foreach ($this->connections as $conn) {
                if($conn !== $connection) $conn->write($text);
            }
        }

    }
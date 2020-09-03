<?php

namespace MyShop {

    class Database {
        public function query() {
            return array('1', '2', '3');
        }
    }

    // @TODO Implement constructor injection DatabaseConstructorConsumer
    // with worker method:
    //
    //    public function doSomething() {
    //        return implode(', ', $this->database->query());
    //    }
    class DatabaseConstructorConsumer{
        public function __construct(Database $database){
            $this->db = $database;
        }
        function doSomething(){
            return implode(', ', $this->db->query())
        }
    }

    // @TODO Implement constructor injection DatabaseSetterConsumer
    // with same worker method above
    class DatabaseSetterConsumer{
        function setDatabase(Database $database){
            $this->db = $database;
        }
        
        function doSomething(){
            return implode(', ', $this->db->query());
        }
    }

}

namespace {

    use MyShop\Database;
    use MyShop\DatabaseConstructorConsumer;
    use MyShop\DatabaseSetterConsumer;

    // constructor injection
    $consumer = new DatabaseConstructorConsumer(new MyShop\Database);
    assert($consumer->doSomething() == '1, 2, 3');

    // setter injection
    $consumer = new DatabaseSetterConsumer;
    $consumer->setDatabase(new MyShop\Database);
    assert($consumer->doSomething() == '1, 2, 3');

}

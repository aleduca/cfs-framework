<?php
namespace app\database\builder;

use app\database\DatabaseConnect;
use Exception;
use PDO;

class DB
{
    private string $table;
    private string $query;

    public function table(string $table)
    {
        $this->table = $table;
        return $this;
    }
    
    public function select($fields = '*')
    {
        if (!isset($this->table)) {
            throw new Exception("To select please give a table name");
        }
        $this->query = "select {$fields} from {$this->table}";
        return $this;
    }

    public function where()
    {
    }

    public function and()
    {
    }

    public function or()
    {
    }

    public function limit()
    {
    }

    public function get()
    {
        try {
            $connection = DatabaseConnect::getConnection();
            $prepare = $connection->prepare($this->query);
            $prepare->execute();

            return $prepare->fetchAll(PDO::FETCH_CLASS, singularizeModel($this->table));
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }
    }

    public function first()
    {
        try {
            $connection = DatabaseConnect::getConnection();
            $this->query.=" order by id desc limit 1";
            $prepare = $connection->prepare($this->query);
            $prepare->execute();

            return $prepare->fetchObject(singularizeModel($this->table));
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }
    }
}

<?php
namespace app\database\builder;

use app\database\DatabaseConnect;
use Exception;
use PDO;

class DB
{
    private string $table;
    private string $select;
    private string $where;
    private string $whereIn;
    private string $order;
    private array $binds = [];
    private string $and;
    private string $or;
    private string $limit;

    private function clearPropertyQuery(string $property):void
    {
        if (property_exists($this, $property)) {
            unset($this->$property);
        } else {
            var_dump("property {$property} does not exist");
        }
    }

    public function table(string $table):self
    {
        $this->table = $table;
        return $this;
    }
    
    public function select($fields = '*'):self
    {
        if (!isset($this->table)) {
            throw new Exception("To select please give a table name");
        }
        $this->select = "select {$fields} from {$this->table}";
        return $this;
    }

    public function where(string $field, string $operator, string|int $value):self
    {
        if (!isset($this->where)) {
            $this->where = " where {$field} {$operator} :{$field}";
            $this->binds[$field] = $value;
        }
        
        if (isset($this->and) && isset($this->where)) {
            $this->where .= " {$this->and} {$field} {$operator} :{$field}";
            $this->binds[$field] = $value;
        }
        
        if (isset($this->or) && isset($this->where)) {
            $this->where .= " {$this->or} {$field} {$operator} :{$field}";
            $this->binds[$field] = $value;
        }

        $this->clearPropertyQuery('and');
        $this->clearPropertyQuery('or');

        return $this;
    }

    public function whereIn(string|int $field, array $data):self
    {
        $whereInConverted = "{$field} IN "."('".implode("','", $data)."')";

        if (!isset($this->where)) {
            $this->whereIn = " where {$whereInConverted}";
        }

        if (isset($this->and) and isset($this->where)) {
            $this->whereIn= "{$this->and} {$whereInConverted}";
        }
        
        if (isset($this->or) and isset($this->where)) {
            $this->whereIn = "{$this->or} {$whereInConverted}";
        }

        $this->clearPropertyQuery('and');
        $this->clearPropertyQuery('or');

        return $this;
    }

    public function order(string $order):self
    {
        $this->order = " order by {$order}";
        
        return $this;
    }

    public function and():self
    {
        $this->and = ' and';
        return $this;
    }
    
    public function or():self
    {
        $this->or = ' or';
        return $this;
    }

    public function limit(int $limit):self
    {
        $this->limit = " limit {$limit}";

        return $this;
    }

    public function get():self
    {
        try {
            $connection = DatabaseConnect::getConnection();
            $query = $this->dump();

            var_dump($query);

            $prepare = $connection->prepare($query);
            
            $prepare->execute($this->binds);

            $this->clearPropertyQuery('binds');
            
            return $prepare->fetchAll(PDO::FETCH_CLASS, singularizeModel($this->table));
        } catch (\Throwable $th) {
            echo $th->getMessage(). ' '.$th->getFile().' '.$th->getLine();
        }
    }
    
    public function first():self
    {
        try {
            $connection = DatabaseConnect::getConnection();
            
            $query = $this->dump();
            $query.=" order by id desc limit 1";
            
            $prepare = $connection->prepare($query);
            $prepare->execute($this->binds);
            
            $this->clearPropertyQuery('binds');

            return $prepare->fetchObject(singularizeModel($this->table));
        } catch (\Throwable $th) {
            echo $th->getMessage(). ' '.$th->getFile().' '.$th->getLine();
        }
    }

    public function dump():string
    {
        $filters = $this->select ?? throw new Exception("Select method is required");
        $filters .= $this->where ?? '';
        $filters .= $this->whereIn ?? '';
        $filters .= $this->order ?? '';
        $filters .= $this->limit ?? '';

        $this->clearPropertyQuery('where');
        $this->clearPropertyQuery('whereIn');
        $this->clearPropertyQuery('order');
        $this->clearPropertyQuery('limit');

        return $filters;
    }
}

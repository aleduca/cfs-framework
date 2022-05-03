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
    private string $group;
    private array $binds = [];
    private string $and;
    private string $join;
    private string $or;
    private string $limit;
    private string $offset;

    private function clearPropertyQuery(string|array $property):void
    {
        if (is_string($property)) {
            if (property_exists($this, $property)) {
                unset($this->$property);
            } else {
                var_dump("property {$property} does not exist");
            }
        }
        
        if (is_array($property)) {
            foreach ($property as $prop) {
                if (property_exists($this, $prop)) {
                    unset($this->$prop);
                } else {
                    var_dump("property {$prop} does not exist");
                }
            }
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
        $replaceField = str_replace('.', '', $field);
        if (!isset($this->where)) {
            $this->where = " where {$field} {$operator} :{$replaceField}";
            $this->binds[$replaceField] = $value;
        }
        
        if (isset($this->and) && isset($this->where)) {
            $this->where .= " {$this->and} {$field} {$operator} :{$replaceField}";
            $this->binds[$replaceField] = $value;
        }
        
        if (isset($this->or) && isset($this->where)) {
            $this->where .= " {$this->or} {$field} {$operator} :{$replaceField}";
            $this->binds[$replaceField] = $value;
        }

        $this->clearPropertyQuery('and');
        $this->clearPropertyQuery('or');

        return $this;
    }

    public function whereIn(string $field, array $data):self
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

    public function group(string $group):self
    {
        $this->group = " group by {$group}";
        return $this;
    }

    public function join(string $table, string $query, $type = 'inner'):self
    {
        if (!isset($this->join)) {
            $this->join = " {$type} join {$table} on {$query}";
        } else {
            $this->join.= " {$type} join {$table} on {$query}";
        }
        
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

    public function offset(int $offset):self
    {
        if (!isset($this->limit)) {
            throw new Exception("Offset need a limit");
        }
        $this->offset = " offset {$offset}";
        return $this;
    }

    public function count()
    {
        $connection = DatabaseConnect::getConnection();
        
        $query = $this->dump();

        $prepare = $connection->prepare($query);
        
        $prepare->execute($this->binds);
        
        $this->clearPropertyQuery('binds');

        return $prepare->rowCount();
    }

    public function total():int
    {
        $connection = DatabaseConnect::getConnection();

        $query = $connection->query($this->select);

        return $query->rowCount();
    }

    public function paginate()
    {
    }

    public function get()
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
    
    public function first()
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
        $filters .= $this->join ?? '';
        $filters .= $this->where ?? '';
        $filters .= $this->whereIn ?? '';
        $filters .= $this->group ?? '';
        $filters .= $this->order ?? '';
        $filters .= $this->limit ?? '';
        $filters .= $this->offset ?? '';

        // You can clear properties with one array or call the clearProperty method to clear one by one.
        $this->clearPropertyQuery(['where','whereIn','order','group','limit','join','offset']);
        // $this->clearPropertyQuery('whereIn');
        // $this->clearPropertyQuery('order');
        // $this->clearPropertyQuery('limit');
        // $this->clearPropertyQuery('group');
        // $this->clearPropertyQuery('join');
        // $this->clearPropertyQuery('offset');

        return $filters;
    }
}

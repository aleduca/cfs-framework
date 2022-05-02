<?php
namespace app\database\models;

use app\database\DatabaseConnect;
use Exception;
use PDO;

class Model
{
    protected array $attributes = [];
    
    public function all($fields = '*')
    {
        try {
            $sql = "select {$fields} from {$this->table}";
            $connection = DatabaseConnect::getConnection();
            $prepare = $connection->prepare($sql);
            $prepare->execute();

            return $prepare->fetchAll(PDO::FETCH_CLASS, get_called_class());
        } catch (\Throwable $th) {
        }
    }

    public function findBy(string $field, string|int $value, string $fields = '*')
    {
        try {
            $sql = "select {$fields} from {$this->table} where {$field} = :{$field}";
            $connection = DatabaseConnect::getConnection();

            $prepare = $connection->prepare($sql);
            $prepare->execute([$field => $value]);
            return $prepare->fetchObject();
        } catch (\Throwable $th) {
        }
    }

    public function __set(string $name, string $value)
    {
        $this->attributes[$name] = $value;
    }

    public function __get($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
    }

    public function update(array $data)
    {
        try {
            $originalData = $data;

            unset($data['id']);

            $sql = "update {$this->table} set ";
            foreach (array_keys($data) as $key) {
                $sql.="{$key} = :{$key},";
            }

            $sql = rtrim($sql, ',');

            $sql .= " where id = :id";

            $connection = DatabaseConnect::getConnection();
            $prepare = $connection->prepare($sql);
            return $prepare->execute($originalData);
        } catch (\Throwable $th) {
            DatabaseConnect::rollback($th);
        }
    }

    public function create(array $data)
    {
        try {
            $sql = "insert into {$this->table}(";
            $sql.=implode(',', array_keys($data)).') values(';
            $sql.=':'.implode(',:', array_keys($data)).')';
        
            $connection = DatabaseConnect::getConnection();
            $prepare = $connection->prepare($sql);
            return $prepare->execute($data);
        } catch (\Throwable $th) {
            DatabaseConnect::rollback($th);
        }
    }

    public function save()
    {
        try {
            return isset($this->attributes['id']) ?
                $this->update($this->attributes):
                $this->create($this->attributes);
        } catch (\Throwable $th) {
            DatabaseConnect::rollback($th);
        }
    }
    
    public function delete(array $data = [])
    {
        try {
            if (isset($this->attributes['id'])) {
                $sql = "delete from {$this->table} where id = :id";
                $data = ['id' => $this->attributes['id']];
            } else {
                if (!isset($data['field'], $data['value'])) {
                    throw new Exception("To delete please give the field and value index to array");
                }
                $sql = "delete from {$this->table} where {$data['field']} = :{$data['field']}";
                $data = [$data['field'] => $data['value']];
            }
            $connection = DatabaseConnect::getConnection();
            $prepare = $connection->prepare($sql);
            return $prepare->execute($data);
        } catch (\Throwable $th) {
            DatabaseConnect::rollback($th);
        }
    }
}

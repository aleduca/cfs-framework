<?php
namespace app\database\models;

use app\database\DatabaseConnect;

class Model
{
    protected array $attributes = [];
    
    public function all($fields = '*')
    {
        try {
            $sql = "select {$fields} from {$this->table}";
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
    }

    public function create(array $data)
    {
        $sql = "insert into {$this->table}(";
        $sql.=implode(',', array_keys($data)).') values(';
        $sql.=':'.implode(',:', array_keys($data)).')';

        $connection = DatabaseConnect::getConnection();
        $prepare = $connection->prepare($sql);
        return $prepare->execute($data);
    }

    public function save()
    {
        try {
            if (isset($this->attributes['id'])) {
                return $this->update($this->attributes);
            }
            return $this->create($this->attributes);
        } catch (\Throwable $th) {
            DatabaseConnect::rollback($th);
        }
    }

    public function delete()
    {
    }
}

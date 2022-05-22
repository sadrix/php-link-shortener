<?php

namespace Models;

use Database\Database;
use Helpers\Collection;

class Model {

    public static $table = '';

    public static $fields = [];

    public static $hidden = [];

    public static $appends = [];

    public static function all() {
        return static::findBySql("SELECT * FROM " . static::$table);
    }

    public static function find(int $id = 0) {
        $collection = static::findBySql("SELECT * FROM " . static::$table . " WHERE id={$id} LIMIT 1");
        return $collection->count() ? $collection->shift() : null;
    }

    public static function create(array $data) {
        if ($data) {
            $sql = "INSERT INTO " . static::$table . " ";
            $keys = [];
            $values = [];
            foreach ($data as $key => $value) {
                if (in_array($key, static::$fields)) {
                    $keys[] = "{$key}";
                    $values[] = "'{$value}'";
                }
            }
            $sql .= "(" . implode(', ', $keys) . ") ";
            $sql .= "VALUES (" . implode(', ', $values) . ")";

            if (db()->query($sql) === true) {
                $last_id = mysqli_insert_id(db()->connection);
                return static::find($last_id);
            } else
                return null;
        }
    }

    public function update(array $data): bool
    {
        if ($data && $this->id) {
            $sql = "UPDATE " . static::$table . " SET ";
            $updates = [];
            foreach ($data as $key => $value) {
                if (in_array($key, static::$fields)) {
                    $updates[] = "{$key} = '{$value}'";
                }
            }
            $sql .= implode(', ', $updates);
            $sql .= " WHERE id = {$this->id}";

            if (db()->query($sql) === true) {
                foreach ($data as $key => $value)
                    $this->$key = $value;

                return true;
            } else
                return false;
        }
    }

    public function delete()
    {
        if ($this->id) {
            $sql = "DELETE FROM " . static::$table . " WHERE id = " . $this->id;
            return db()->query($sql);
        }
        return false;
    }

    public static function findBySql($sql = "") : Collection
    {
        $result_set = db()->query($sql);
        $collection = new Collection();
        while ($row = db()->fetch_array($result_set)) {
            $collection->add(static::instantiate($row));
        }
        return $collection;
    }

    public static function instantiate($record)
    {
        $object = new static;
        foreach ($record as $attribute => $value) {
            if ($object->has_field($attribute)) {
                $object->$attribute = $value;
            }
        }
        return $object;
    }

    public function has_field($attribute)
    {
        return in_array($attribute, static::$fields);
    }

    public function toArray() {
        $array = [];
        foreach (static::$fields as $field)
            if (is_array(static::$hidden) && !in_array($field, static::$hidden))
                $array[$field] = $this->$field;

        if ((static::$appends))
            foreach (static::$appends as $attr) {
                $name = dashesToCamelCase($attr, true);
                $method_name = "get{$name}Attribute";
                $value = method_exists($this, $method_name) ? $this->$method_name() : null;
                if (property_exists($this, $attr)) {
                    $this->$attr = $value;
                    $array[$attr] = $value;
                }
            }
        return $array;
    }

}
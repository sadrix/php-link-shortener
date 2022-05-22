<?php

namespace Database;

use mysqli;

class Database {

    public $connection;

    public $last_query;

    function __construct()
    {
        $this->open_connection();
    }

    public function open_connection()
    {
        $server = config('database/server');
        $username = config('database/username');
        $password = config('database/password');
        $db_name = config('database/db_name');

        $connection = mysqli_connect($server, $username, $password, $db_name);

        if ($connection->connect_error)
            die("Connection failed: " . $connection->connect_error);
        
        else
            mysqli_set_charset($connection, 'utf8');

        $this->connection = $connection;
    }

    public function close_connection()
    {
        if (isset($this->connection)) {
            mysqli_close($this->connection);
            unset($this->connection);
        }
    }

    public function query($sql)
    {
        $this->last_query = $sql;
        $result = mysqli_query($this->connection, $sql);
        $this->confirm_query($result);
        return $result;
    }

    public function count($sql)
    {
        $result = $this->query($sql);
        return $result ? mysqli_num_rows($result) : 0;
    }

    // this is a checking function for query() method and becuse it just use in this class we make its scope to private
    private function confirm_query($result)
    {
        if (!$result)
            return response(500, 'Server error.', [
                'error' => "Database query failed: " . $this->connection->error,
                'query' => "Last SQL query was: " . $this->last_query
            ]);
    }

    public function scape_value($value)
    {
        $value = trim($value);
        $value = mysqli_real_escape_string($this->connection, $value);
        return $value;
    }

    public function fetch_array($result)
    {
        return mysqli_fetch_assoc($result);
    }

    public function num_rows($result)
    {
        return mysqli_num_rows($result);
    }

    public function insert_id()
    {
        return mysqli_insert_id($this->connection);
    }

    public function next_id($table_name)
    {
        $sql = "SHOW TABLE STATUS LIKE '{$table_name}'";
        $run = $this->query($sql);
        $row = $this->fetch_array($run);
        return $row['Auto_increment'];
    }

    public function affected_rows()
    {
        return mysqli_affected_rows($this->connection);
    }
}
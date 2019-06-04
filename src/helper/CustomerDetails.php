<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 03-06-2019
 * Time: 12:56
 */

namespace MiniOrange\Helper;

use Illuminate\Support\Facades\DB as LaraDB;

class CustomerDetails
{
    public static function get_option($key)
    {
        try {
            $result = LaraDB::select('select * from mo_customer_details where id = ?', [1])[0];
        } catch (\PDOException $e) {
            if ($e->getCode() == '42S02') {
                header('Location: create_tables');
                exit;
            }
        } catch (\Exception $e) {
            $code = $e->getCode();
            $msg = $e->getMessage();
            $trace = $e->getTraceAsString();
            echo " $code \r\n $msg \r\n $trace \r\n";
            $env_connection = getenv('DB_CONNECTION');
            $env_database = getenv('DB_DATABASE');
            $env_host = getenv('DB_HOST');
            echo " $env_connection \r\n\ $env_database \r\n $env_host";
            exit;
        }
        //var_dump($result);exit;
        return $result->$key;
    }

    public static function update_option($key, $value)
    {
        try {
            $result = LaraDB::table('mo_customer_details')->updateOrInsert([
                'id' => 1
            ], [
                $key => $value
            ]);
        } catch (\Exception $e) {
            $code = $e->getCode();
            $msg = $e->getMessage();
            $trace = $e->getTraceAsString();
            $trace = serialize($trace);

            echo " $code \r\n $msg \r\n $trace";
            $env_connection = getenv('DB_CONNECTION');
            $env_database = getenv('DB_DATABASE');
            $env_host = getenv('DB_HOST');
            echo " $env_connection \r\n\ $env_database \r\n $env_host";
            exit;
        }
    }

    public static function delete_option($key)
    {
        try {
            $result = LaraDB::table('mo_customer_details')->updateOrInsert([
                'id' => 1
            ], [
                $key => ''
            ]);
        } catch (\Exception $e) {
            $code = $e->getCode();
            $msg = $e->getMessage();
            $trace = $e->getTraceAsString();
            $trace = serialize($trace);
            echo " $code \r\n $msg \r\n $trace";
            $env_connection = getenv('DB_CONNECTION');
            $env_database = getenv('DB_DATABASE');
            $env_host = getenv('DB_HOST');
            echo " $env_connection \r\n\ $env_database \r\n $env_host";
            exit;
        }
    }

    protected static function get_options()
    {
        try {
            $result = LaraDB::select('select * from mo_customer_details')[0];
        } catch (\Exception $e) {
            $code = $e->getCode();
            $msg = $e->getMessage();
            $trace = $e->getTraceAsString();
            echo " $code \r\n $msg \r\n $trace";
            $env_connection = getenv('DB_CONNECTION');
            $env_database = getenv('DB_DATABASE');
            $env_host = getenv('DB_HOST');
            echo " $env_connection \r\n\ $env_database \r\n $env_host";
            exit;
        }
    }
}
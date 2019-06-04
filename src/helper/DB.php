<?php
namespace MiniOrange\Helper;

use Illuminate\Database\Capsule\Manager as ConDB;
use Illuminate\Support\Facades\DB as LaraDB;
use MiniOrange\Classes\Actions\DatabaseController as DC;
use Illuminate\Routing\Controller;
use Illuminate\Contracts\Console\Kernel as Kernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;
use PDOException;
use phpDocumentor\Reflection\Types\Null_;

class DB extends Controller
{

    public static function get_option($key)
    {
        try {
            $result = LaraDB::select('select * from mo_config where id = ?', [1])[0];
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
        return $result->$key;
    }

    public static function update_option($key, $value)
    {
        try {
            $result = LaraDB::table('mo_config')->updateOrInsert([
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
            $result = LaraDB::table('mo_config')->updateOrInsert([
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

    public static function get_registered_user()
    {
        try {
            $result = LaraDB::select('select * from mo_admin')[0];
        } catch (\PDOException $e) {
            if ($e->getCode() == '42S02') {
                header('Location: create_tables');
                exit;
            }
        }
        if (empty($result->email))
            return null;
        else
            return $result;
    }

    public static function register_user($email, $password)
    {
        try {
            LaraDB::table('mo_admin')->updateOrInsert([
                'id' => 1
            ], [
                'email' => $email,
                'password' => $password
            ]);
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

    public static function get_column($table, $key)
    {
        self::startConnection();
        $option = ConDB::table($table)->first()->$key;
        return $option;
    }

    protected static function startConnection()
    {
        $path = realpath(__DIR__.'/../../../../laravel/framework/src/Illuminate/Support/helpers.php');
        include_once $path;

        $connection = array(
            'driver' => getenv('DB_CONNECTION'),
            'host' => getenv('DB_HOST'),
            'port' => getenv('DB_PORT'),
            'database' => getenv('DB_DATABASE'),
            'username' => getenv('DB_USERNAME'),
            'password' => getenv('DB_PASSWORD'),
            'collation' => 'default'
        );

        $Capsule = new ConDB();
        $Capsule->addConnection($connection);
        $Capsule->setAsGlobal(); // this is important. makes the database manager object globally available
        $Capsule->bootEloquent();
        try {
            if (ConDB::table('mo_config')->get() == NULL) {
                ConDB::table('mo_config')->updateOrInsert([
                    'id' => 1
                ], [
                    'mo_saml_host_name' => 'https://auth.miniorange.com'
                ]);
            }
        } catch (PDOException $e) {

            if ($e->getCode() === '42S02') {

                header('Location: create_tables');
                exit();
            }
            if ($e->getCode() == 2002) {
                echo 'It looks like your <b>Database is offline</b>. Please make sure that your database is up and running, and try again.<a style="text-decoration:none" href="/"><u>Click here to go back to your website</u></a>';
                exit();
            }
        }
    }

    protected static function get_options()
    {
        try {
            $result = LaraDB::select('select * from mo_config')[0];
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

?>

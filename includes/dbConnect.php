<?php
/**
 * Created by PhpStorm.
 * User: Thomas
 * Date: 27/12/2017
 * Time: 11:41
 */



class DbConnect
{
    //Database link
    private $con;


    function __contruct()
    {

    }

    function connect()
    {
        include_once dirname(__FILE__). "/constants.php";
        $this->con = new \mysqli(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_NAME);

        if (mysqli_connect_errno())
        {
            echo 'failed to connect to MySql'.mysqli_connect_error();
            return null;
        }

        return $this->con;
    }


}
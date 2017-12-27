<?php
/**
 * Created by PhpStorm.
 * User: Thomas
 * Date: 27/12/2017
 * Time: 11:53
 */

class DbOperation
{
    private $con;

    /**
     * DbOperation constructor.
     */
    function __construct()
    {
        require_once dirname(__FILE__) . '/DbConnect.php';
        $db = new DbConnect();
        $this->con = $db->connect();
    }

    function getListPoint()
    {
        $stmt = $this->con->prepare("SELECT * FROM point");
        $stmt->execute();
        $stmt->bind_result($id, $fic, $depart_id, $latitude, $longitude, $statut_work, $distance_to_do, $distance_done, $abbatage);
        $listPoint = array();

        while ($stmt->fetch())
        {
            $temp = array();
            $temp['id'] = $id;
            $temp['fic'] = $fic;
            $temp['depart_id'] = $depart_id;
            $temp['latitude'] = $latitude;
            $temp['longitude'] = $longitude;
            $temp['statut_work'] = $statut_work;
            $temp['distance_to_do'] = $distance_to_do;
            $temp['distance_done'] = $distance_done;
            $temp['abbatage'] = $abbatage;

            $listPoint[] = $temp;
        }
        return $listPoint;
    }


}
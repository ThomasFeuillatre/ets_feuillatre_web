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
        $stmt = $this->con->prepare("SELECT * FROM points");
        $stmt->execute();
        $stmt->bind_result($id, $fic, $depart_id, $latitude, $longitude, $statut_work, $distance_to_do, $distance_done, $abattage);
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
            $temp['abattage'] = $abattage;

            $listPoint[] = $temp;
        }
        return $listPoint;
    }

    //Method for user login
    function userLogin($name, $pass)
    {
        //$password = md5($pass);
        $stmt = $this->con->prepare("SELECT id FROM worker WHERE worker.name = ? AND worker.password = ?");
        $stmt->bind_param("ss", $name, $pass);
        $stmt->execute();
        $stmt->bind_result($id_worker);
        $stmt->fetch();
        return $id_worker;
    }

    function saveTimeClock($id_worker, $day_time, $am_start, $am_end, $pm_start, $pm_end, $place, $statut, $observation)
    {
        $query = "INSERT INTO clock_time(id_worker,dayDate, am_start, am_end, pm_start, pm_end, place, statut, observation) VALUES($id_worker,'$day_time', '$am_start', '$am_end', '$pm_start', '$pm_end', '$place', $statut, '$observation')";
        $stmt = $this->con->query($query);
        if($stmt)
        {
            return true;
        }
        return false;
    }

    function checkStatut($id_worker, $day_time)
    {
        $stmt = $this->con->prepare("SELECT statut FROM clock_time WHERE id_worker = ? AND day_time = ?");
        $stmt->bind_param("is", $id_worker, $day_time);
        $stmt->execute();
        $stmt->bind_result($statut);
        $result = $stmt->get_result()->fetch_array();
        print_r($result);
        if($result['statut'] != 0) {
            return true;
        }
        else {
            return false;

        }
    }

    function getDataClockPoint($id_worker, $day_time)
    {
        $stmt = $this->con->prepare("SELECT am_start, am_end, pm_start, pm_end, place, statut, observation FROM clock_time WHERE id_worker = ? AND day_time = ?");
        $stmt->bind_param("is", $id_worker, $day_time);
        $stmt->execute();
        $stmt->bind_result($amStart, $amEnd, $pmStart, $pmEnd, $place, $statut, $observation);
        $result = $stmt->get_result();
        return $result->fetch_array();
    }
}
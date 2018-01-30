<?php

/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Ravi Tamada
 * @link URL Tutorial link
 */
class DbHandler {

    private $conn;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    /**
     * Fetching all the events
     */
    public function getAllEvents() {
        $query = "SELECT * FROM event";
        $res = array();

        if ($result = $this->conn->query($query)) {
            
            while ($row = $result->fetch_row()) {
                if($row[8] == 0){
                    $limited = false;
                } else {
                    $limited = true;
                }

                $language_array = explode(",",$row[14]);
                
                $res[] = array( 'event' => array(
                        'ID' => $row[0],
                        'title' => $row[1],
                        'location' => $row[2],
                        'date' => $row[3],
                        'time' => $row[4],
                        'ageLimit' => array($row[5], $row[6]),
                        'groupSize' => $row[7],
                        'limited' => $limited,
                        'maxParticipants' => $row[9],
                        'joining' => $row[10],
                        'description' => $row[11],
                        'img' => $row[12],
                        'type' => $row[13],
                        'language' => $language_array));
                }
            
            $result->close();
            return $res;
        } else {
            return NULL;
        }
    }

    /**
     * Fetching single event
     * @param String $event_id id of the event
     */
    public function getEvent($event_id) {
        $query = "SELECT * FROM event WHERE ID = " . $event_id;
        $res = array();
        if ($result = $this->conn->query($query)) {
            while ($row = $result->fetch_row()) {
                if($row[8] == 0){
                    $limited = false;
                } else {
                    $limited = true;
                }

                $language_array = explode(",",$row[14]);
                
                $res[] = array( 'event' => array(
                        'ID' => $row[0],
                        'title' => $row[1],
                        'location' => $row[2],
                        'date' => $row[3],
                        'time' => $row[4],
                        'ageLimit' => array($row[5], $row[6]),
                        'groupSize' => $row[7],
                        'limited' => $limited,
                        'maxParticipants' => $row[9],
                        'joining' => $row[10],
                        'description' => $row[11],
                        'img' => $row[12],
                        'type' => $row[13],
                        'language' => $language_array));
                }
            
            $result->close();
            return $res;
        } else {
            return NULL;
        }
    }

}

?>

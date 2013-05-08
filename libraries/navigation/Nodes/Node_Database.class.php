<?php
/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 * Functionality for the navigation tree
 *
 * @package PhpMyAdmin-Navigation
 */
if (! defined('PHPMYADMIN')) {
    exit;
}

/**
 * Represents a database node in the navigation tree
 *
 * @package PhpMyAdmin-Navigation
 */
class Node_Database extends Node
{
    /**
     * Initialises the class
     *
     * @param string $name     An identifier for the new node
     * @param int    $type     Type of node, may be one of CONTAINER or OBJECT
     * @param bool   $is_group Whether this object has been created
     *                         while grouping nodes
     *
     * @return Node_Database
     */
    public function __construct($name, $type = Node::OBJECT, $is_group = false)
    {
        parent::__construct($name, $type, $is_group);
        $this->icon  = PMA_Util::getImage('s_db.png');
        $this->links = array(
            'text' => 'db_structure.php?server=' . $GLOBALS['server']
                    . '&amp;db=%1$s&amp;token=' . $GLOBALS['token'],
            'icon' => 'db_operations.php?server=' . $GLOBALS['server']
                    . '&amp;db=%1$s&amp;token=' . $GLOBALS['token']
        );
    }

    /**
     * Returns the number of children of type $type present inside this container
     * This method is overridden by the Node_Database and Node_Table classes
     *
     * @param string $type         The type of item we are looking for
     *                             ('tables', 'views', etc)
     * @param string $searchClause A string used to filter the results of the query
     *
     * @return int
     */
    public function getPresence($type = '', $searchClause = '')
    {
        $retval = 0;
        $db     = $this->real_name;
        switch ($type) {
        case 'tables':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $db     = PMA_Util::sqlAddSlashes($db);
                $query  = "SELECT COUNT(*) ";
                $query .= "FROM `INFORMATION_SCHEMA`.`TABLES` ";
                $query .= "WHERE `TABLE_SCHEMA`='$db' ";
                $query .= "AND `TABLE_TYPE`='BASE TABLE' ";
                if (! empty($searchClause)) {
                    $query .= "AND `TABLE_NAME` LIKE '%";
                    $query .= PMA_Util::sqlAddSlashes(
                        $searchClause, true
                    );
                    $query .= "%'";
                }
                $retval = (int)PMA_DBI_fetchValue($query);
            } else {
                $query  = "SHOW FULL TABLES FROM ";
                $query .= PMA_Util::backquote($db);
                $query .= " WHERE `Table_type`='BASE TABLE' ";
                if (! empty($searchClause)) {
                    $query .= "AND " . PMA_Util::backquote(
                        "Tables_in_" . $db
                    );
                    $query .= " LIKE '%" . PMA_Util::sqlAddSlashes(
                        $searchClause, true
                    );
                    $query .= "%'";
                }
                $retval = PMA_DBI_numRows(PMA_DBI_tryQuery($query));
            }
            break;
        case 'views':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $db     = PMA_Util::sqlAddSlashes($db);
                $query  = "SELECT COUNT(*) ";
                $query .= "FROM `INFORMATION_SCHEMA`.`TABLES` ";
                $query .= "WHERE `TABLE_SCHEMA`='$db' ";
                $query .= "AND `TABLE_TYPE`!='BASE TABLE' ";
                if (! empty($searchClause)) {
                    $query .= "AND `TABLE_NAME` LIKE '%";
                    $query .= PMA_Util::sqlAddSlashes(
                        $searchClause, true
                    );
                    $query .= "%'";
                }
                $retval = (int)PMA_DBI_fetchValue($query);
            } else {
                $query  = "SHOW FULL TABLES FROM ";
                $query .= PMA_Util::backquote($db);
                $query .= " WHERE `Table_type`!='BASE TABLE' ";
                if (! empty($searchClause)) {
                    $query .= "AND " . PMA_Util::backquote(
                        "Tables_in_" . $db
                    );
                    $query .= " LIKE '%" . PMA_Util::sqlAddSlashes(
                        $searchClause, true
                    );
                    $query .= "%'";
                }
                $retval = PMA_DBI_numRows(PMA_DBI_tryQuery($query));
            }
            break;
        case 'procedures':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $db     = PMA_Util::sqlAddSlashes($db);
                $query  = "SELECT COUNT(*) ";
                $query .= "FROM `INFORMATION_SCHEMA`.`ROUTINES` ";
                $query .= "WHERE `ROUTINE_SCHEMA`='$db'";
                $query .= "AND `ROUTINE_TYPE`='PROCEDURE' ";
                if (! empty($searchClause)) {
                    $query .= "AND `ROUTINE_NAME` LIKE '%";
                    $query .= PMA_Util::sqlAddSlashes(
                        $searchClause, true
                    );
                    $query .= "%'";
                }
                $retval = (int)PMA_DBI_fetchValue($query);
            } else {
                $db    = PMA_Util::sqlAddSlashes($db);
                $query = "SHOW PROCEDURE STATUS WHERE `Db`='$db' ";
                if (! empty($searchClause)) {
                    $query .= "AND `Name` LIKE '%";
                    $query .= PMA_Util::sqlAddSlashes(
                        $searchClause, true
                    );
                    $query .= "%'";
                }
                $retval = PMA_DBI_numRows(PMA_DBI_tryQuery($query));
            }
            break;
        case 'functions':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $db     = PMA_Util::sqlAddSlashes($db);
                $query  = "SELECT COUNT(*) ";
                $query .= "FROM `INFORMATION_SCHEMA`.`ROUTINES` ";
                $query .= "WHERE `ROUTINE_SCHEMA`='$db' ";
                $query .= "AND `ROUTINE_TYPE`='FUNCTION' ";
                if (! empty($searchClause)) {
                    $query .= "AND `ROUTINE_NAME` LIKE '%";
                    $query .= PMA_Util::sqlAddSlashes(
                        $searchClause, true
                    );
                    $query .= "%'";
                }
                $retval = (int)PMA_DBI_fetchValue($query);
            } else {
                $db    = PMA_Util::sqlAddSlashes($db);
                $query = "SHOW FUNCTION STATUS WHERE `Db`='$db' ";
                if (! empty($searchClause)) {
                    $query .= "AND `Name` LIKE '%";
                    $query .= PMA_Util::sqlAddSlashes(
                        $searchClause, true
                    );
                    $query .= "%'";
                }
                $retval = PMA_DBI_numRows(PMA_DBI_tryQuery($query));
            }
            break;
        case 'events':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $db     = PMA_Util::sqlAddSlashes($db);
                $query  = "SELECT COUNT(*) ";
                $query .= "FROM `INFORMATION_SCHEMA`.`EVENTS` ";
                $query .= "WHERE `EVENT_SCHEMA`='$db' ";
                if (! empty($searchClause)) {
                    $query .= "AND `EVENT_NAME` LIKE '%";
                    $query .= PMA_Util::sqlAddSlashes(
                        $searchClause, true
                    );
                    $query .= "%'";
                }
                $retval = (int)PMA_DBI_fetchValue($query);
            } else {
                $db    = PMA_Util::backquote($db);
                $query = "SHOW EVENTS FROM $db ";
                if (! empty($searchClause)) {
                    $query .= "WHERE `Name` LIKE '%";
                    $query .= PMA_Util::sqlAddSlashes(
                        $searchClause, true
                    );
                    $query .= "%'";
                }
                $retval = PMA_DBI_numRows(PMA_DBI_tryQuery($query));
            }
            break;
        default:
            break;
        }
        return $retval;
    }

    /**
     * Returns the names of children of type $type present inside this container
     * This method is overridden by the Node_Database and Node_Table classes
     *
     * @param string $type         The type of item we are looking for
     *                             ('tables', 'views', etc)
     * @param int    $pos          The offset of the list within the results
     * @param string $searchClause A string used to filter the results of the query
     *
     * @return array
     */
    public function getData($type, $pos, $searchClause = '')
    {
        $maxItems = $GLOBALS['cfg']['MaxNavigationItems'];
        $retval   = array();
        $db       = $this->real_name;
        switch ($type) {
        case 'tables':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $db     = PMA_Util::sqlAddSlashes($db);
                $query  = "SELECT `TABLE_NAME` AS `name` ";
                $query .= "FROM `INFORMATION_SCHEMA`.`TABLES` ";
                $query .= "WHERE `TABLE_SCHEMA`='$db' ";
                $query .= "AND `TABLE_TYPE`='BASE TABLE' ";
                if (! empty($searchClause)) {
                    $query .= "AND `TABLE_NAME` LIKE '%";
                    $query .= PMA_Util::sqlAddSlashes(
                        $searchClause, true
                    );
                    $query .= "%'";
                }
                $query .= "ORDER BY `TABLE_NAME` ASC ";
                $query .= "LIMIT " . intval($pos) . ", $maxItems";
                $retval = PMA_DBI_fetchResult($query);
            } else {
                $query  = " SHOW FULL TABLES FROM ";
                $query .= PMA_Util::backquote($db);
                $query .= " WHERE `Table_type`='BASE TABLE' ";
                if (! empty($searchClause)) {
                    $query .= "AND " . PMA_Util::backquote(
                        "Tables_in_" . $db
                    );
                    $query .= " LIKE '%" . PMA_Util::sqlAddSlashes(
                        $searchClause, true
                    );
                    $query .= "%'";
                }
                $handle = PMA_DBI_tryQuery($query);
                if ($handle !== false) {
                    $count = 0;
                    while ($arr = PMA_DBI_fetchArray($handle)) {
                        if ($pos <= 0 && $count < $maxItems) {
                            $retval[] = $arr[0];
                            $count++;
                        }
                        $pos--;
                    }
                }
            }
            break;
        case 'views':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $db     = PMA_Util::sqlAddSlashes($db);
                $query  = "SELECT `TABLE_NAME` AS `name` ";
                $query .= "FROM `INFORMATION_SCHEMA`.`TABLES` ";
                $query .= "WHERE `TABLE_SCHEMA`='$db' ";
                $query .= "AND `TABLE_TYPE`!='BASE TABLE' ";
                if (! empty($searchClause)) {
                    $query .= "AND `TABLE_NAME` LIKE '%";
                    $query .= PMA_Util::sqlAddSlashes(
                        $searchClause, true
                    );
                    $query .= "%'";
                }
                $query .= "ORDER BY `TABLE_NAME` ASC ";
                $query .= "LIMIT " . intval($pos) . ", $maxItems";
                $retval = PMA_DBI_fetchResult($query);
            } else {
                $query  = "SHOW FULL TABLES FROM ";
                $query .= PMA_Util::backquote($db);
                $query .= " WHERE `Table_type`!='BASE TABLE' ";
                if (! empty($searchClause)) {
                    $query .= "AND " . PMA_Util::backquote(
                        "Tables_in_" . $db
                    );
                    $query .= " LIKE '%" . PMA_Util::sqlAddSlashes(
                        $searchClause, true
                    );
                    $query .= "%'";
                }
                $handle = PMA_DBI_tryQuery($query);
                if ($handle !== false) {
                    $count = 0;
                    while ($arr = PMA_DBI_fetchArray($handle)) {
                        if ($pos <= 0 && $count < $maxItems) {
                            $retval[] = $arr[0];
                            $count++;
                        }
                        $pos--;
                    }
                }
            }
            break;
        case 'procedures':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $db     = PMA_Util::sqlAddSlashes($db);
                $query  = "SELECT `ROUTINE_NAME` AS `name` ";
                $query .= "FROM `INFORMATION_SCHEMA`.`ROUTINES` ";
                $query .= "WHERE `ROUTINE_SCHEMA`='$db'";
                $query .= "AND `ROUTINE_TYPE`='PROCEDURE' ";
                if (! empty($searchClause)) {
                    $query .= "AND `ROUTINE_NAME` LIKE '%";
                    $query .= PMA_Util::sqlAddSlashes(
                        $searchClause, true
                    );
                    $query .= "%'";
                }
                $query .= "ORDER BY `ROUTINE_NAME` ASC ";
                $query .= "LIMIT " . intval($pos) . ", $maxItems";
                $retval = PMA_DBI_fetchResult($query);
            } else {
                $db    = PMA_Util::sqlAddSlashes($db);
                $query = "SHOW PROCEDURE STATUS WHERE `Db`='$db' ";
                if (! empty($searchClause)) {
                    $query .= "AND `Name` LIKE '%";
                    $query .= PMA_Util::sqlAddSlashes(
                        $searchClause, true
                    );
                    $query .= "%'";
                }
                $handle = PMA_DBI_tryQuery($query);
                if ($handle !== false) {
                    $count = 0;
                    while ($arr = PMA_DBI_fetchArray($handle)) {
                        if ($pos <= 0 && $count < $maxItems) {
                            $retval[] = $arr['Name'];
                            $count++;
                        }
                        $pos--;
                    }
                }
            }
            break;
        case 'functions':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $db     = PMA_Util::sqlAddSlashes($db);
                $query  = "SELECT `ROUTINE_NAME` AS `name` ";
                $query .= "FROM `INFORMATION_SCHEMA`.`ROUTINES` ";
                $query .= "WHERE `ROUTINE_SCHEMA`='$db' ";
                $query .= "AND `ROUTINE_TYPE`='FUNCTION' ";
                if (! empty($searchClause)) {
                    $query .= "AND `ROUTINE_NAME` LIKE '%";
                    $query .= PMA_Util::sqlAddSlashes(
                        $searchClause, true
                    );
                    $query .= "%'";
                }
                $query .= "ORDER BY `ROUTINE_NAME` ASC ";
                $query .= "LIMIT " . intval($pos) . ", $maxItems";
                $retval = PMA_DBI_fetchResult($query);
            } else {
                $db    = PMA_Util::sqlAddSlashes($db);
                $query = "SHOW FUNCTION STATUS WHERE `Db`='$db' ";
                if (! empty($searchClause)) {
                    $query .= "AND `Name` LIKE '%";
                    $query .= PMA_Util::sqlAddSlashes(
                        $searchClause, true
                    );
                    $query .= "%'";
                }
                $handle = PMA_DBI_tryQuery($query);
                if ($handle !== false) {
                    $count = 0;
                    while ($arr = PMA_DBI_fetchArray($handle)) {
                        if ($pos <= 0 && $count < $maxItems) {
                            $retval[] = $arr['Name'];
                            $count++;
                        }
                        $pos--;
                    }
                }
            }
            break;
        case 'events':
            if (! $GLOBALS['cfg']['Servers'][$GLOBALS['server']]['DisableIS']) {
                $db     = PMA_Util::sqlAddSlashes($db);
                $query  = "SELECT `EVENT_NAME` AS `name` ";
                $query .= "FROM `INFORMATION_SCHEMA`.`EVENTS` ";
                $query .= "WHERE `EVENT_SCHEMA`='$db' ";
                if (! empty($searchClause)) {
                    $query .= "AND `EVENT_NAME` LIKE '%";
                    $query .= PMA_Util::sqlAddSlashes(
                        $searchClause, true
                    );
                    $query .= "%'";
                }
                $query .= "ORDER BY `EVENT_NAME` ASC ";
                $query .= "LIMIT " . intval($pos) . ", $maxItems";
                $retval = PMA_DBI_fetchResult($query);
            } else {
                $db    = PMA_Util::backquote($db);
                $query = "SHOW EVENTS FROM $db ";
                if (! empty($searchClause)) {
                    $query .= "WHERE `Name` LIKE '%";
                    $query .= PMA_Util::sqlAddSlashes(
                        $searchClause, true
                    );
                    $query .= "%'";
                }
                $handle = PMA_DBI_tryQuery($query);
                if ($handle !== false) {
                    $count = 0;
                    while ($arr = PMA_DBI_fetchArray($handle)) {
                        if ($pos <= 0 && $count < $maxItems) {
                            $retval[] = $arr['Name'];
                            $count++;
                        }
                        $pos--;
                    }
                }
            }
            break;
        default:
            break;
        }
        return $retval;
    }


    /**
     * Returns the comment associated with node
     * This method should be overridden by specific type of nodes
     *
     * @return string
     */
    public function getComment()
    {
        return PMA_getDbComment($this->real_name);
    }
}

?>
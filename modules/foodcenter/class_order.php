<?php




class ordered
{
    public $orders;
    
    public function read_orders($userid)
    {
        global $db;
        
        $result = $db->qry("SELECT * FROM %prefix%food_ordering WHERE userid=%int% GROUP BY ordertime ORDER BY ordertime", $userid);

        while ($row = $db->fetch_array($result)) {
            $orders[] .= $row['ordertime'];
        }
    }
    
    public function read_user_ordered($userid)
    {
        $query = $db->qry("SELECT * FROM %prefix%food_ordering WHERE status = 1 AND userid=%int%", $auth['userid']);
    }
}

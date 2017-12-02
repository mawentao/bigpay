<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class table_wxconnect_pay_order extends discuz_table 
{
	public function __construct() {
		$this->_table = 'wxconnect_pay_order';
		$this->_pk = 'order_id';
		parent::__construct();
	}

    public function getByOrderId($orderid)
    {
        $sql = "SELECT * FROM ".DB::table($this->_table)." WHERE order_id='$orderid'";
        $row = DB::fetch_first($sql);
        if (!empty($row)) {
            return $row;
        }
        return array();
    }

    public function create($body, $fee, $addby='wxconnect')
    {
        $row = DB::fetch_first('SELECT order_id FROM %t WHERE order_body=%s AND total_fee=%d', array($this->_table, $body, $fee));
        if (!empty($row)) {
            return $row['order_id'];
        }
        $data = array (
            'order_body' => $body,
            'total_fee'  => $fee,
            'addtime'    => time(),
            'addby'      => $addby,
        );
        return parent::insert($data, true);
    }

    public function save()
    {
        $order_id   = wxconnect_validate::getNCParameter('order_id','order_id','integer');
        $order_body = wxconnect_validate::getNCParameter('order_body','order_body','string',32);
        $total_fee  = wxconnect_validate::getNCParameter('total_fee','total_fee','integer');
        if ($total_fee==0) {
            throw new Exception("订单金额不能为0");
        }
        $row = DB::fetch_first('SELECT order_id FROM %t WHERE order_body=%s AND total_fee=%d', array($this->_table, $order_body, $total_fee));
        if (!empty($row)) {
            if ($row['order_id']==$order_id) {
                return;
            } else {
                throw new Exception("已存在同名同价订单");
            }
        }
        if ($order_id==0) {
			$data = array (
				'order_body' => $order_body,
				'total_fee'  => $total_fee,
				'addtime'    => time(),
				'addby'      => 'admin',
			);
			return parent::insert($data, true);
        } else {
            $sql = "UPDATE ".DB::table($this->_table)." SET order_body='$order_body',total_fee='$total_fee' WHERE order_id=$order_id";
            return DB::query($sql);
        }
    }

    public function query()
    {
		$return = array(
            "totalProperty" => 0,
            "root" => array(),
        );
        $key    = wxconnect_validate::getNCParameter('key','key','string');
        $addby  = wxconnect_validate::getOPParameter('addby','addby','string',1024,'');
        $sort   = wxconnect_validate::getOPParameter('sort','sort','string',1024,'order_id');
        $dir    = wxconnect_validate::getOPParameter('dir','dir','string',1024,'DESC');
        $start  = wxconnect_validate::getOPParameter('start','start','integer',1024,0);
        $limit  = wxconnect_validate::getOPParameter('limit','limit','integer',1024,0);
        $where = "1";
        if ($key!="") {
			$where.= " AND (order_body like '%$key%')";
        }
        if ($addby!='') {
            $where.=" AND addby='$addby'";
        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS order_id,order_body,total_fee,addtime,addby ".
               "FROM ".DB::table($this->_table)." ".
               "WHERE $where ".
               "ORDER BY `$sort` $dir ".
               "LIMIT $start,$limit";
        $query = DB::query($sql);
		while($row = DB::fetch($query)) {
            $row["addtime"] = date("Y-m-d H:i:s", $row["addtime"]);
            $row["order_body"] = iconv(CHARSET, "UTF-8//ignore", $row["order_body"]);
            $row['total_fee'] = number_format($row['total_fee']/100, 2);
			$return["root"][] = $row;
		}
        $query = DB::query("select FOUND_ROWS() AS total");
        if ($row = DB::fetch($query)) {
            $return["totalProperty"] = $row["total"];
        }
        return $return;
    }

    public function del()
    {
        $ids = wxconnect_validate::getNCParameter('ids','ids','string');
        $sql = "DELETE FROM ".DB::table($this->_table)." WHERE order_id in ($ids)";
        DB::query($sql);
        $sql = "DELETE FROM ".DB::table('wxconnect_pay_log')." WHERE order_id in ($ids)";
        DB::query($sql);
    }
}
// vim600: sw=4 ts=4 fdm=marker syn=php
?>

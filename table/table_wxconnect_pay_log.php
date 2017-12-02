<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
class table_wxconnect_pay_log extends discuz_table 
{
	public function __construct() {
		$this->_table = 'wxconnect_pay_log';
		$this->_pk = 'pay_id';
		parent::__construct();
	}

    // 创建支付记录（生成支付页面前调用）
    public function create($orderid, $openid, $outer_id, $pay_fee)
    {
        $realid = $orderid;
        $data = array (
            'order_id' => $realid,
            'openid'   => $openid,
            'outer_id' => $outer_id,
            'pay_fee'  => $pay_fee,
            'paytime'  => time(),
            'detail'   => '',
            'status'   => 0,
        );
        return parent::insert($data, true);
    }

    // 更新支付结果（支付完成后回调更新）
    // status: 1:success,2:fail
    public function update_result($payid, $status, $detail)
    {
        $sql = "UPDATE ".DB::table($this->_table)." SET status=$status, detail='$detail' WHERE pay_id=$payid AND status=0";
        DB::query($sql);
    }

    // 查询支付记录
	public function query()
    {
		$return = array(
            "totalProperty" => 0,
            "root" => array(),
        );
        $key    = wxconnect_validate::getNCParameter('key','key','string');
        $status = wxconnect_validate::getNCParameter('status','status','integer');
        $sort   = wxconnect_validate::getOPParameter('sort','sort','string',1024,'paytime');
        $dir    = wxconnect_validate::getOPParameter('dir','dir','string',1024,'DESC');
        $start  = wxconnect_validate::getOPParameter('start','start','integer',1024,0);
        $limit  = wxconnect_validate::getOPParameter('limit','limit','integer',1024,0);
        $where = "status=$status";
        if ($key!="") {
            if (is_numeric($key)) {
                $where.= " AND a.order_id='$key'";
            } else {
			    $where.= " AND (b.order_body like '%$key%')";
            }
        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS a.*,b.order_body ".
               "FROM ".DB::table($this->_table)." AS a JOIN dz_wxconnect_pay_order AS b on a.order_id=b.order_id ".
               "WHERE $where ".
               "ORDER BY `$sort` $dir ".
               "LIMIT $start,$limit";
        $query = DB::query($sql);
		while($row = DB::fetch($query)) {
            $row["paytime"] = date("Y-m-d H:i:s", $row["paytime"]);
            $row["order_body"] = iconv(CHARSET, "UTF-8//ignore", $row["order_body"]);
            $row['pay_fee'] = number_format($row['pay_fee']/100, 2);
			$return["root"][] = $row;
		}
        $query = DB::query("select FOUND_ROWS() AS total");
        if ($row = DB::fetch($query)) {
            $return["totalProperty"] = $row["total"];
        }
        return $return;
    }

}
// vim600: sw=4 ts=4 fdm=marker syn=php
?>

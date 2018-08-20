<?php
require_once(WP_PLUGIN_DIR . '/mts-simple-booking-c/mtssb-list-admin.php');
require_once('support-list-table.php');

class SupportBookingList extends MTSSB_List_Admin
{
    private static $suport_List = null;

    /**
	 * インスタンス化
	 *
	 */
	static function get_instance() {
		if (!isset(self::$suport_List)) {
			self::$suport_List = new SupportBookingList();
		}

		return self::$suport_List;
	}

    /**
     * 管理画面メニュー処理
     *
     */
    public function list_page() {
        $this->page_action();
        
		$this->blist = new SupportListTable();
        $this->blist->prepare_items($this);
        include('support-table.php');
    }

    private function page_action()
    {
        $this->errflg = false;
        $this->message = '';

        $this->action = 'none';
        $this->themonth = mktime(0, 0, 0, date_i18n('n'), 1, date_i18n('Y'));

        if (isset($_GET['action'])) {
          $this->action = $_GET['action'];
        }

        switch ($this->action) {
            case 'monthly' :
                if (isset($_GET['year']) && isset($_GET['month'])) {
                    $this->themonth = mktime(0, 0, 0, intval($_GET['month']), 1, intval($_GET['year']));
                }
                break;
            case 'delete' :
                // NONCEチェックOKなら削除する
                if (wp_verify_nonce($_GET['nonce'], self::PAGE_NAME . "_{$this->action}")) {
                    if ($this->del_booking($_GET['booking_id'])) {
                        $this->message = sprintf(__('Booking ID:%d was deleted.', $this->domain), $_GET['booking_id']);
                    } else {
                        $this->message = __('Deleting the booking data was failed.', $this->domain);
                        $this->errflg = true;
                    }
                } else {
                    $this->message = 'Nonce check error.';
                    $this->errflg = true;
                }
                // ページネーションのリンクにdeleteが残るのでURLをクリアする
                $_SERVER['REQUEST_URI'] = remove_query_arg(array('booking_id', 'action', 'nonce'));
                break;
            default:
                break;
        }
    }

    	/**
	 * スケジュール月間指定フォームの出力
	 */
	private function _select_form() {

		$this_year = date_i18n('Y');
		$this_month = date_i18n('n');
		$this_time = mktime(0, 0, 0, $this_month, 1, $this_year);

		$theyear = date('Y', $this->themonth);
		$themonth = date('n', $this->themonth);

		// リンク
		$prev_month = mktime(0, 0, 0, $themonth - 1, 1, $theyear);
		$prev_str = date('Y-m', $prev_month);
		$next_month = mktime(0, 0, 0, $themonth + 1, 1, $theyear);
        $next_str = date('Y-m', $next_month);
        include('support-select-form.php');
    }

    /**
	 * 予約データを取得する
	 *
	 * @offset
	 * @limit
	 * @order
	 * @article_id
	 */
	public function get_booking_list($offset, $limit, $order, $conditions='1=1') { //article_id=0) {
		global $wpdb;

		//$conditions = 1 < intval($article_id) ? sprintf('article_id=%d', $article_id) : '1=1';
		

		$sql = $wpdb->prepare("
			SELECT booking_id,booking_time,confirmed,parent_id,article_id,user_id,number,options,client,created,
				Post.post_title AS article_name
			FROM $this->tblBooking
			JOIN {$wpdb->posts} AS Post ON article_id=Post.ID
			WHERE $conditions AND user_id<>-1
			ORDER BY {$order['key']} {$order['direction']}
			LIMIT %d, %d", $offset, $limit);

		$data = $wpdb->get_results($sql, ARRAY_A);

		foreach ($data as $key => $booking) {
			$data[$key]['options'] = unserialize($booking['options']);
            $data[$key]['client'] = unserialize($booking['client']);
            $data[$key]['member_id'] = $this->get_member_id($data[$key]['client']['email']);
		}

		return $data;
    }
    
    public function get_member_id($clientEmail)
    {
        global $wpdb;
        $sql = "SELECT member_id 
                FROM {$wpdb->prefix}swpm_members_tbl 
                WHERE email='{$clientEmail}'";
        return $wpdb->get_var($sql);  
    }
}
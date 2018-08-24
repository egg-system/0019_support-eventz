<?php

require_once(WP_PLUGIN_DIR . '/mts-simple-booking-c/mtssb-list-admin.php');

class SupportListTable extends MTSSB_Booking_List
{
    /**
     * リストカラム情報
     *
     */
    public function get_columns() 
    {
        return array(
          'booking_id' => __('ID', 'mts_simple_booking'),
          'booking_time' => __('Booking Date', 'mts_simple_booking'),
          'member_id' => '会員ID',
          'name' => __('Name'),
          'number' => __('Number', 'mts_simple_booking'),
          'article_id' => __('Article Name', 'mts_simple_booking'),
          'confirmed' => __('Confirmed', 'mts_simple_booking'),
          'created' => __('Date'),
        );
    }

    /*
    * カラムデータのデフォルト表示
    *
    */
    public function column_default($item, $column_name) 
    {
        if ($column_name !== 'member_id') {
            return parent::column_default($item, $column_name);
        }

        if (is_null($item[$column_name])) {
            return '非会員';
        }
        
        $member_id = $item[$column_name];
        $href = "admin.php?page=simple_wp_membership&member_action=edit&member_id={$member_id}";
        $href = admin_url($href);
        return "<a href='{$href}'>{$member_id}</a>";
    }
}
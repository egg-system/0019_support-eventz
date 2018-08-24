<?php

require_once('support-booking-list.php');

add_action('admin_menu', 'alter_booking_list', 11);
function alter_booking_list()
{
    remove_submenu_page('simple-booking', 'simple-booking-list');
    add_submenu_page('simple-booking', '予約リスト', '予約リスト', 'administrator', 'support-booking-list', 'createSupportBookList');
}

function createSupportBookList()
{
    $bookingList = SupportBookingList::get_instance();
    $bookingList->list_page();
}
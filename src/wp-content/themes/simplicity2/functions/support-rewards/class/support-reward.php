<?php

class SupportReward {
    private $attribute = [];

    public function __construct($array)
    {
        $this->attribute = $array;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->attribute)) {
            return $this->attribute[$name];
        }

        return null;
    } 

    public function __set($name, $value)
    {
        $this->attribute[$name] = $value;
    }

    public function toArray()
    {
        return $this->attribute;
    }

    public static function getRewards($date, $isOnlyWithdrawal)
    {
        global $wpdb;
        $sql = 'SELECT 
            member_table.member_id,
            concat(
                member_table.first_name, 
                member_table.last_name
            ) AS member_name,
            wp9_reward_details.introducer_id,
            concat(
                introducer_table.first_name, 
                introducer_table.last_name
            ) AS introducer_name,
            `date`,
            wp9_swpm_membership_tbl.alias AS introducer_category,
            price
        FROM wp9_reward_details
        LEFT JOIN wp9_swpm_members_tbl AS member_table
        ON wp9_reward_details.member_id = member_table.member_id
        LEFT JOIN wp9_swpm_members_tbl AS introducer_table
        ON wp9_reward_details.introducer_id = introducer_table.member_id
        LEFT JOIN wp9_swpm_membership_tbl
        ON wp9_reward_details.level = wp9_swpm_membership_tbl.id';

        $whereSql = self::getWhereSql($date, $isOnlyWithdrawal);
        if (isset($whereSql)) {
            $sql = "{$sql}\n WHERE {$whereSql}";
        }

        $sql = "{$sql}\n ORDER BY `date` DESC";
        $rewards = $wpdb->get_results($sql, 'ARRAY_A');

        $resultRewards = [];
        foreach ($rewards as $reward) {
            $resultRewards[] = new self($reward);
        }

        return $resultRewards;
    }

    private static function getWhereSql($date, $isOnlyWithdrawal)
    {
        $whereSql = [];
        if (isset($date)) {
            $whereSql[] = "`date` LIKE '{$date}%'";
        }

        if ($isOnlyWithdrawal) {
            $whereSql[] = "introducer_id IS NULL";
        }
		
        return empty($whereSql) ? null : implode($whereSql, ' and ');
    }
}
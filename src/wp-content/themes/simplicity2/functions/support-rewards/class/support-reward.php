<?php

class SupportReward {
    private $attribute = [];

    public function __construct($array)
    {
        $this->attribute = $array;
    }

    public function __get($name)
    {
        var_dump($this->attribute);
        exit;

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

    public static function getRewards($startMonth, $endMonth)
    {
        global $wpdb;
        $members = $wpdb->get_results(self::getMembersSql(), 'ARRAY_A');
        $rewards = $wpdb->get_results(self::getRewardsSql($startMonth, $endMonth), 'ARRAY_A');

        $results = null;
        foreach ($members as $member) {
            $memberId = $member['id'];
            $reward = self::getMemberReward($memberId, $rewards);

            $memberWithReqard = array_merge($member, $reward);
            $results[$memberId] = new self($memberWithReqard);
        }

        return $results;
    }

    private static function getMembersSql()
    {
        return "SELECT 
                    wp9_swpm_members_tbl.member_id as id,
                    concat(
                        wp9_swpm_members_tbl.first_name, 
                        wp9_swpm_members_tbl.last_name
                    ) as member_name,
                    wp9_swpm_members_tbl.member_since as member_created_at,
                    wp9_swpm_members_tbl.subscription_starts as member_category_chage,
                    wp9_swpm_membership_tbl.alias as member_category
                FROM wp9_swpm_members_tbl
                LEFT JOIN wp9_swpm_membership_tbl
                ON wp9_swpm_members_tbl.membership_level = wp9_swpm_membership_tbl.id
                ORDER BY id DESC";
    }

    private static function getRewardsSql($startMonth, $endMonth)
    {
        return "SELECT 
                    member_id,
                    DATE_FORMAT(`date`, '%Y-%m') as `month`,
                    SUM(price) as month_reward
                FROM wp9_reward_details
                WHERE `date` between '{$startMonth->format('Y-m-d')}' 
                    AND '{$endMonth->format('Y-m-d')}' 
                GROUP BY member_id, `month`";
    }

    private static function getMemberReward($memberId, $rewards)
    {
        $resultReward = [];
        foreach ($rewards as $reward) {
            if ($reward['member_id'] !== $memberId) {
                continue;
            }

            $resultReward[$reward['month']] = $reward['month_reward'];
        }

        return $resultReward;
    }
}
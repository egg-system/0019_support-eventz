<?php
namespace AutoReg;

require_once(__DIR__ . "/../constant.php");
require_once(__DIR__ . "/dao.php");

class AutoRegUtils
{

  /**
   * テレコムアクセスチェック
   *
   * @return boolean
   */
  public static function isTelecomIpAccessed($remoteIp) {
      return in_array($remoteIp, Constant::TELECOM_IP_FROM_TO, true);
  }


  /**
   * 会員料金取得
   *
   * @return int
   */
  public static function getMemberFee($memberLevel) {
      // 5,6,7のいずれかの場合
      if ($memberLevel == Constant::UNPAID_PREMIUM_MEMBER) return Constant::PREMIUM_MEMBER_FEE; // 5000
      if ($memberLevel == Constant::UNPAID_PREMIUM_AGENCY) return Constant::PREMIUM_AGENCY_FEE; // 8000
      if ($memberLevel == Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER) return Constant::PREMIUM_AGENCY_ORGANIZER_FEE; // 8000
      if ($memberLevel == Constant::UNPAID_PREMIUM_MEMBER_WEST) return Constant::PREMIUM_MEMBER_FEE_WEST; // 2000
      if ($memberLevel == Constant::UNPAID_PREMIUM_AGENCY_WEST) return Constant::PREMIUM_AGENCY_FEE_WEST; // 4000
      if ($memberLevel == Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER_WEST) return Constant::PREMIUM_AGENCY_ORGANIZER_FEE_WEST; // 4000
      return null;
  }


  /**
   * 会員レベル取得
   *
   * @return int
   */
  public static function getMemberLevel($memberLevel) {
      // 5,6,7のいずれかの場合、8,9,10
      if ($memberLevel == Constant::UNPAID_PREMIUM_MEMBER) return Constant::PREMIUM_MEMBER_LEVEL;
      if ($memberLevel == Constant::UNPAID_PREMIUM_AGENCY) return Constant::PREMIUM_AGENCY_LEVEL;
      if ($memberLevel == Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER) return Constant::PREMIUM_AGENCY_ORGANIZER_LEVEL;
      return null;
  }


  /**
   * 決済未済会員レベル取得
   *
   * @return int
   */
  public static function getUnpaidMemberLevel($level) {
      // 2回以上決済失敗の場合は5,6,7のいずれかとなる為、そのまま返す
      if ($level <= Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER) return $level;
      // 8,9,10いずれかの場合、5,6,7を返す
      if ($level == Constant::PREMIUM_MEMBER_LEVEL) return Constant::UNPAID_PREMIUM_MEMBER;
      if ($level == Constant::PREMIUM_AGENCY_LEVEL) return Constant::UNPAID_PREMIUM_AGENCY;
      if ($level == Constant::PREMIUM_AGENCY_ORGANIZER_LEVEL) return Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER;
      return null;
  }


  /**
   * 報酬金額取得
   *
   * @return int
   */
  public static function getRewardPrice($level) {
    switch ($level) {
      // 関東
      case Constant::PREMIUM_MEMBER_LEVEL:
        return Constant::PREMIUM_MEMBER_INTRODUCE_FEE; // 2000
      case Constant::PREMIUM_AGENCY_LEVEL:
        return Constant::PREMIUM_AGENCY_INTRODUCE_FEE; // 4000
      case Constant::PREMIUM_AGENCY_ORGANIZER_LEVEL:
        return Constant::PREMIUM_AGENCY_ORGANIZER_INTRODUCE_FEE; // 4000
      // 関西
      case Constant::PREMIUM_AGENCY_LEVEL_WEST:
        return Constant::PREMIUM_AGENCY_INTRODUCE_FEE_WEST; // 1000
      case Constant::PREMIUM_AGENCY_ORGANIZER_LEVEL_WEST:
        return Constant::PREMIUM_AGENCY_ORGANIZER_INTRODUCE_FEE_WEST; // 2000
      default:
        return null;
    }
  }

} // end of class


?>

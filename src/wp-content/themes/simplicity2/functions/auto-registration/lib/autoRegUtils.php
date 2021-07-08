<?php
namespace AutoReg;

require_once(__DIR__ . "/../constant.php");
require_once(__DIR__ . "/dao.php");

class AutoRegUtils {

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
      // 関東
      if ($memberLevel == Constant::UNPAID_PREMIUM_MEMBER) return Constant::PREMIUM_MEMBER_FEE; // 5000
      if ($memberLevel == Constant::UNPAID_PREMIUM_AGENCY) return Constant::PREMIUM_AGENCY_FEE; // 8000
      if ($memberLevel == Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER) return Constant::PREMIUM_AGENCY_ORGANIZER_FEE; // 8000
      // 11,12,13のいずれかの場合、14,15,16
      // 関西
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
      // 関東
      if ($memberLevel == Constant::UNPAID_PREMIUM_MEMBER) return Constant::PREMIUM_MEMBER_LEVEL;
      if ($memberLevel == Constant::UNPAID_PREMIUM_AGENCY) return Constant::PREMIUM_AGENCY_LEVEL;
      if ($memberLevel == Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER) return Constant::PREMIUM_AGENCY_ORGANIZER_LEVEL;
      // 11,12,13のいずれかの場合、14,15,16
      // 関西
      if ($memberLevel == Constant::UNPAID_PREMIUM_MEMBER_WEST) return Constant::PREMIUM_MEMBER_LEVEL_WEST;
      if ($memberLevel == Constant::UNPAID_PREMIUM_AGENCY_WEST) return Constant::PREMIUM_AGENCY_LEVEL_WEST;
      if ($memberLevel == Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER_WEST) return Constant::PREMIUM_AGENCY_ORGANIZER_LEVEL_WEST;

      // (継続決済用)決済会員の場合はそのレベルをそのまま返す
      if ($memberLevel == Constant::PREMIUM_MEMBER_LEVEL) return $memberLevel;
      if ($memberLevel == Constant::PREMIUM_AGENCY_LEVEL) return $memberLevel;
      if ($memberLevel == Constant::PREMIUM_AGENCY_ORGANIZER_LEVEL) return $memberLevel;
      if ($memberLevel == Constant::PREMIUM_MEMBER_LEVEL_WEST) return $memberLevel;
      if ($memberLevel == Constant::PREMIUM_AGENCY_LEVEL_WEST) return $memberLevel;
      if ($memberLevel == Constant::PREMIUM_AGENCY_ORGANIZER_LEVEL_WEST) return $memberLevel;

      return null;
  }


  /**
   * 決済未済会員レベル取得
   *
   * @return int
   */
  public static function getUnpaidMemberLevel($level) {
      // 2回以上決済失敗の場合は5,6,7 or 11,12,13のいずれかとなる為、そのまま返す
      if ($level == Constant::UNPAID_PREMIUM_MEMBER) return $level;
      if ($level == Constant::UNPAID_PREMIUM_AGENCY) return $level;
      if ($level == Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER) return $level;
      if ($level == Constant::UNPAID_PREMIUM_MEMBER_WEST) return $level;
      if ($level == Constant::UNPAID_PREMIUM_AGENCY_WEST) return $level;
      if ($level == Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER_WEST) return $level;

      // 8,9,10いずれかの場合、5,6,7を返す
      // 関東
      if ($level == Constant::PREMIUM_MEMBER_LEVEL) return Constant::UNPAID_PREMIUM_MEMBER;
      if ($level == Constant::PREMIUM_AGENCY_LEVEL) return Constant::UNPAID_PREMIUM_AGENCY;
      if ($level == Constant::PREMIUM_AGENCY_ORGANIZER_LEVEL) return Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER;
      // 11,12,13のいずれかの場合、14,15,16
      // 関西
      if ($level == Constant::PREMIUM_MEMBER_LEVEL_WEST) return Constant::UNPAID_PREMIUM_MEMBER_WEST;
      if ($level == Constant::PREMIUM_AGENCY_LEVEL_WEST) return Constant::UNPAID_PREMIUM_AGENCY_WEST;
      if ($level == Constant::PREMIUM_AGENCY_ORGANIZER_LEVEL_WEST) return Constant::UNPAID_PREMIUM_AGENCY_ORGANIZER_WEST;
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
      case Constant::PREMIUM_MEMBER_LEVEL_WEST:
        return Constant::PREMIUM_MEMBER_INTRODUCE_FEE_WEST; // 1000
      case Constant::PREMIUM_AGENCY_LEVEL_WEST:
        return Constant::PREMIUM_AGENCY_INTRODUCE_FEE_WEST; // 2000
      case Constant::PREMIUM_AGENCY_ORGANIZER_LEVEL_WEST:
        return Constant::PREMIUM_AGENCY_ORGANIZER_INTRODUCE_FEE_WEST; // 2000
      default:
        return null;
    }
  }

} // end of class


?>

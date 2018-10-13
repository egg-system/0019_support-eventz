<?php
/*
 * 初回決済時配信メール内容
 *
 * PHPのバージョンが低いと__DIR__をconstで使うとParse errorになるが
 * includeで使えば問題ないらしいので使う
 */

namespace AutoReg;

class MailConstant
{
  // 定数定義
  // プレミアム会員メール
  const PREMIUM_MEMBER_MAIL01 = "<br>サポートイベント　プレミアム会員のご登録ありがとうございます。
                                 <br>本メールはご登録者の方のみにお送りしております。
                                 <br>
                                 <br>1）マイページ利用について
                                 <br>下記ページからログイン後、マイページのご利用が可能となります。
                                 <br>  → https://support.eventz.jp/membership-login/membership-profile
                                 <br>
                                 <br> ※マイページでは「登録情報の変更」「報酬金額の確認」「出金申請」を行うことができます。
                                 <br> ※会員ID及びパスワードは、ご登録の際にご自身で設定したものになります。
                                 <br>";

  const PREMIUM_MEMBER_MAIL02 = "<br>2）プレミアムイベント参加申込について【プレミアムイベント参加し放題】
                                 <br>公式HPのプレミアムイベント案内文からIDとパスワードを入力の上、
                                 <br>会員ログインを行い、イベントへのお申込みをお願い致します。
                                 <br>
                                 <br>こちらのお申込み方法でお申込み頂けると会員としての受付がスムーズとなります。
                                 <br>　→　公式HP　http://support.eventz.jp/
                                 <br>
                                 <br>※会員ID及びパスワードは、ご登録の際にご自身で設定したものになります。
                                 <br>
                                 <br>
                                 <br>3）サポートイベントプレミアム会員・代理店サービス資料
                                 <br>こちらからダウンロードをお願い致します。
                                 <br>  →　https://support.eventz.jp/download-outline
                                 <br>
                                 <br>
                                 <br>4)その他
                                 <br>イベント主催やプレミアム代理店会員への会員区分変更については、
                                 <br>メールにてご連絡を宜しくお願い致します。
                                 <br>メールアドレス：cafesuppo.kaiin@gmail.com
                                 <br>
                                 <br>
                                 <br>それでは今後共宜しくお願い致します。
                                 <br>
                                 <br>サポートイベント　プレミアムイベント事務局";

  // プレミアム代理店会員 or プレミアム代理店会員&主催メール
  const PREMIUM_AGENCIES_MEMBER_MAIL01 = "<br>サポートイベント　プレミアム代理店会員のご登録ありがとうございます。
                                          <br>本メールはご登録者の方のみにお送りしております。
                                          <br>
                                          <br>
                                          <br>早速代理店活動していただくために
                                          <br>重要事項をご連絡いたしますので最後まで確認をお願いします。
                                          <br>
                                          <br>
                                          <br>まず、代理店の資格を有する方は以下を利用することが出来ます。
                                          <br>
                                          <br>
                                          <br>1）マイページ利用について
                                          <br>下記ページからログイン後、マイページのご利用が可能となります。
                                          <br>  → https://support.eventz.jp/membership-login/membership-profile
                                          <br>
                                          <br> ※マイページでは「登録情報の変更」「報酬金額の確認」「出金申請」を行うことができます。
                                          <br> ※会員ID及びパスワードは、ご登録の際にご自身で設定したものになります。
                                          <br>";

  const PREMIUM_AGENCIES_MEMBER_MAIL02 = "<br>2）プレミアムイベント参加申込について【プレミアムイベント参加し放題】
                                          <br>公式HPのプレミアムイベント案内文からIDとパスワードを入力の上、
                                          <br>会員ログインを行い、イベントへのお申込みをお願い致します。
                                          <br>
                                          <br>こちらのお申込み方法でお申込み頂けると会員としての受付がスムーズとなります。
                                          <br>　→　公式HP　http://support.eventz.jp/
                                          <br>
                                          <br>※会員ID及びパスワードは、ご登録の際にご自身で設定したものになります。
                                          <br>
                                          <br>
                                          <br>3）プレミアム会員・代理店会員の新規ご登録の権限付与について
                                          <br>代理店会員の方の紹介で、新規の方が代理店会員やプレミアム会員を
                                          <br>ご登録するとストック報酬が発生します。
                                          <br>
                                          <br>プレミアム会員→2,000円/月、代理店会員→4,000円/月
                                          <br>
                                          <br>添付の販促資料を適宜利用下さいますようよろしくお願いいたします。
                                          <br>
                                          <br>プレミアム会員・代理店会員の登録、お申込みはこちらのURLを使用ください。
                                          <br>
                                          <br>・プレミアム会員登録フォーム
                                          <br>　→ https://support.eventz.jp/memberregistration
                                          <br>
                                          <br>・プレミアム代理店会員登録フォーム
                                          <br>　→ https://support.eventz.jp/memberregistration-dairiten
                                          <br>  ※紹介者コードは登録時に届く「会員No.」がお自身のコードになります。
                                          <br>
                                          <br>
                                          <br>4）プレミアムイベント主催の権利
                                          <br>プレミアムイベントの開催のサポートを致します。
                                          <br>
                                          <br>・弊社負担でのコリュパ広告掲載、1万以上アクセスがある弊社ホームページへの広告掲載
                                          <br> (サポートで送客した分の売上は全て主催者の利益とします)
                                          <br>・申込フォーム作成
                                          <br>・イベントスタッフ派遣
                                          <br>・イベント会場紹介無料
                                          <br>・イベンターのマッチング無料
                                          <br>・LINE@(フォロワー2,704)告知
                                          <br>
                                          <br>
                                          <br>5）定型店舗(居酒屋)の格安利用の権利
                                          <br>全国数千店舗展開しているチェーン店を代理店の方限定で格安利用の
                                          <br>権利を付与いたします。
                                          <br>利用する際はプレミアム代理店用公式LINE＠からご相談願います。
                                          <br>
                                          <br>
                                          <br>6）CLC（カフェラウンジクラブ）※期間限定キャンペーン
                                          <br>CLCは定価3,480円(税別)で、都内の提携カフェで商談時のコーヒーが
                                          <br>無料で利用できるサービスです。
                                          <br>この月額費用をサポート代理店限定で”無料”提供させて頂きます。
                                          <br>
                                          <br>ホームページURL：http://www.c-lounge.club
                                          <br>
                                          <br>【申込方法】：
                                          <br>　　①LINE＠友達追加⇒https://goo.gl/6vtKrk
                                          <br>　　②友達追加後、自動メッセージに紹介団体「サポートイベント」、ご自身の氏名を明記の上、返信。
                                          <br>　　　　例：紹介団体⇒サポートイベント
                                          <br>　　　　　　氏名　　⇒山田太郎
                                          <br>　　③上記②のメッセージ返信後、登録手順が届くので、そこから登録。
                                          <br>
                                          <br>　　※LINE@に登録しただけでは完了しませんのでご注意ください。
                                          <br>
                                          <br>
                                          <br>7）プレミアム代理店用公式LINE＠友達追加について
                                          <br>こちらはプレミアムイベント代理店会員専用のLINE@になります。
                                          <br>下記URLからLINE＠の友達追加をお願いします。
                                          <br>
                                          <br>　→ https://line.me/R/ti/p/%40osg0628l
                                          <br>
                                          <br>LINE＠からプレミアムイベントの案内、ID及びパスワードの変更のお知らせ、代理店様からの出金の連絡などが出来ます。
                                          <br>プレミアムイベント事務局とのやりとりがメッセージ上で出来るため
                                          <br>不明な点がある方はこちらのLINE@のメッセージからご連絡ください。
                                          <br>出金を希望される方は「出金希望」とのメッセージをこちらからご連絡下さい。
                                          <br>
                                          <br>
                                          <br>8）サポートイベントプレミアム会員・代理店サービス資料
                                          <br>こちらからダウンロードをお願い致します。
                                          <br>　→　https://support.eventz.jp/download-outline
                                          <br>
                                          <br>
                                          <br>不明点などございましたら、上記LINE＠からメッセージを
                                          <br>お願いいたいます。
                                          <br>
                                          <br>
                                          <br>それでは今後共宜しくお願い致します。
                                          <br>
                                          <br>
                                          <br>サポートイベントプレミアムイベント事務局";

}

?>

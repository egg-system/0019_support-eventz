/*
 * Translated default messages for the jQuery validation plugin.
 * Locale: EN (English)
 * Region: US (United States)
 */
(function($) {
	$.extend($.validator.messages, {
		required: 'この項目は必須項目です。',
		remote: 'この項目を修正して下さい。',
		email: '正しいメールアドレスを入力して下さい。',
		url: '正しいURLを入力して下さい。',
		date: '正しい日付を入力して下さい。',
		dateISO: '正しい日付を入力して下さい (ISO)。',
		number: '正しい数字を入力して下さい。',
		digits: '正しい数字を入力して下さい。',
		creditcard: '正しいクレジットカード番号を入力して下さい。',
		equalTo: '同じ値を入力して下さい。',
		maxlength: $.validator.format( '{0} 文字以下で入力して下さい。' ),
		minlength: $.validator.format( '{0} 文字以上で入力して下さい。' ),
		rangelength: $.validator.format( '{0} 〜 {1} 文字で入力して下さい。' ),
		range: $.validator.format( '{0} 〜 {1} 文字で入力して下さい。' ),
		max: $.validator.format( '{0}以下の値を入力して下さい。' ),
		min: $.validator.format( '{0}以上の値を入力して下さい。' ),
		maxWords: $.validator.format( '{0}以下の単語を入力して下さい。' ),
		minWords: $.validator.format( '{0}以上の単語を入力して下さい。' ),
		rangeWords: $.validator.format( '{0}〜{1}の単語を入力して下さい。' ),
		alphanumeric: '文字、数字、アンダースコアのみ入力できます。',
		lettersonly: '文字のみ入力できます。',
		nowhitespace: '空白は利用できません。',
		phone: '有効な電話番号を入力して下さい。',
		ipv4: '有効なIPv4を入力して下さい。',
		ipv6: '有効なIPv6を入力して下さい。',
		ziprange: '郵便番号が不正です。',
		zipcodeUS: '郵便番号が不正です。',
		integer: '数字を入力して下さい。',
		swpmUsername: 'そのユーザーはすでに登録されています。別のユーザー名を入力して下さい。',
		strongPassReq: 'パスワードは少なくとも1つの小文字、1つの大文字を含む必要があります。'
	});
}(jQuery));

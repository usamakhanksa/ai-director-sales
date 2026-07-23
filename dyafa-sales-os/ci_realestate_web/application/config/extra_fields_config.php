<?php






$config['static_pages'] = array('homepage' => 'Home Page',
								'search' => 'Search Properties',
								'property-for-sale' => 'Search Property for Sale',
								'property-for-rent' => 'Search Property for Rent',
								'all_properties' => 'All Properties',
								'agents' => 'Agents',
								'blog' => 'Blog',
								'blog_single' => 'Single Blog',
								'blog_category' => 'Blog Category',
								'contact' => 'Contact Us',
								'register' => 'Register');/**/


$config['seo_fields'] = array('home_page' => 'Home Page',
					'property_for_sale' => 'Property for Sale Page',
					'property_for_rent' => 'Property for Rent Page',
					'register' => 'Register Page',
					'property' => 'All Property Page',
					'blog' => 'All Blog Page',
					'agent' => 'All Agent Page',
					);
					
					
$social_medias = array();
$social_medias ['facebook'] = array('placeholder' => 'Enter Facebook Url',
									'type' => 'text', 'title' => 'Facebook', 'fa-icon' => 'fa-facebook',);
$social_medias ['google_plus'] = array('placeholder' => 'Enter Google+ Url',
									'type' => 'text', 'title' => 'Google+',	 'fa-icon' => 'fa-google-plus',);
$social_medias ['twitter'] = array('placeholder' => 'Enter Twitter Url',
									'type' => 'text', 'title' => 'Twitter', 'fa-icon' => 'fa-twitter',	);
$social_medias ['pinterest'] = array('placeholder' => 'Enter Pinterest Url',
									'type' => 'text', 'title' => 'Pinterest', 'fa-icon' => 'fa-pinterest',	);
$social_medias ['instagram'] = array('placeholder' => 'Enter Instagram Url',
									'type' => 'text', 'title' => 'Instagram', 'fa-icon' => 'fa-instagram',	);
$social_medias ['youtube'] = array('placeholder' => 'Enter Youtube Url',
										'type' => 'text', 'title' => 'Youtube', 'fa-icon' => 'fa-youtube',);
$config['social_medias'] = $social_medias;






					

$config['currency_symbols'] = array(
	'AED' => '&#1583;.&#1573;', // ?
	'AFN' => '&#65;&#102;',
	'ALL' => '&#76;&#101;&#107;',
	'AMD' => '',
	'ANG' => '&#402;',
	'AOA' => '&#75;&#122;', // ?
	'ARS' => '&#36;',
	'AUD' => '&#36;',
	'AWG' => '&#402;',
	'AZN' => '&#1084;&#1072;&#1085;',
	'BAM' => '&#75;&#77;',
	'BBD' => '&#36;',
	'BDT' => '&#2547;', // ?
	'BGN' => '&#1083;&#1074;',
	'BHD' => '.&#1583;.&#1576;', // ?
	'BIF' => '&#70;&#66;&#117;', // ?
	'BMD' => '&#36;',
	'BND' => '&#36;',
	'BOB' => '&#36;&#98;',
	'BRL' => '&#82;&#36;',
	'BSD' => '&#36;',
	'BTN' => '&#78;&#117;&#46;', // ?
	'BWP' => '&#80;',
	'BYR' => '&#112;&#46;',
	'BZD' => '&#66;&#90;&#36;',
	'CAD' => '&#36;',
	'CDF' => '&#70;&#67;',
	'CHF' => '&#67;&#72;&#70;',
	'CLF' => '', // ?
	'CLP' => '&#36;',
	'CNY' => '&#165;',
	'COP' => '&#36;',
	'CRC' => '&#8353;',
	'CUP' => '&#8396;',
	'CVE' => '&#36;', // ?
	'CZK' => '&#75;&#269;',
	'DJF' => '&#70;&#100;&#106;', // ?
	'DKK' => '&#107;&#114;',
	'DOP' => '&#82;&#68;&#36;',
	'DZD' => '&#1583;&#1580;', // ?
	/*'EGP' => '&#163;',*/
	'EGP' => 'EGP',
	'ETB' => '&#66;&#114;',
	'EUR' => '&#8364;',
	'FJD' => '&#36;',
	'FKP' => '&#163;',
	'GBP' => '&#163;',
	'GEL' => '&#4314;', // ?
	'GHS' => '&#162;',
	'GIP' => '&#163;',
	'GMD' => '&#68;', // ?
	'GNF' => '&#70;&#71;', // ?
	'GTQ' => '&#81;',
	'GYD' => '&#36;',
	'HKD' => 'HK&#36;',
	'HNL' => '&#76;',
	'HRK' => '&#107;&#110;',
	'HTG' => '&#71;', // ?
	'HUF' => '&#70;&#116;',
	'IDR' => '&#82;&#112;',
	'ILS' => '&#8362;',
	'INR' => '&#8377;',
	'IQD' => '&#1593;.&#1583;', // ?
	'IRR' => '&#65020;',
	'ISK' => '&#107;&#114;',
	'JEP' => '&#163;',
	'JMD' => '&#74;&#36;',
	'JOD' => '&#74;&#68;', // ?
	'JPY' => '&#165;',
	'KES' => '&#75;&#83;&#104;', // ?
	'KGS' => '&#1083;&#1074;',
	'KHR' => '&#6107;',
	'KMF' => '&#67;&#70;', // ?
	'KPW' => '&#8361;',
	'KRW' => '&#8361;',
	'KWD' => '&#1583;.&#1603;', // ?
	'KYD' => '&#36;',
	'KZT' => '&#1083;&#1074;',
	'LAK' => '&#8365;',
	'LBP' => '&#163;',
	'LKR' => '&#8360;',
	'LRD' => '&#36;',
	'LSL' => '&#76;', // ?
	'LTL' => '&#76;&#116;',
	'LVL' => '&#76;&#115;',
	'LYD' => '&#1604;.&#1583;', // ?
	'MAD' => '&#1583;.&#1605;.', //?
	'MDL' => '&#76;',
	'MGA' => '&#65;&#114;', // ?
	'MKD' => '&#1076;&#1077;&#1085;',
	'MMK' => '&#75;',
	'MNT' => '&#8366;',
	'MOP' => '&#77;&#79;&#80;&#36;', // ?
	'MRO' => '&#85;&#77;', // ?
	'MUR' => '&#8360;', // ?
	'MVR' => '.&#1923;', // ?
	'MWK' => '&#77;&#75;',
	'MXN' => '&#36;',
	'MYR' => '&#82;&#77;',
	'MZN' => '&#77;&#84;',
	'NAD' => '&#36;',
	'NGN' => '&#8358;',
	'NIO' => '&#67;&#36;',
	'NOK' => '&#107;&#114;',
	'NPR' => '&#8360;',
	'NZD' => '&#36;',
	'OMR' => '&#65020;',
	'PAB' => '&#66;&#47;&#46;',
	'PEN' => '&#83;&#47;&#46;',
	'PGK' => '&#75;', // ?
	'PHP' => '&#8369;',
	'PKR' => '&#8360;',
	'PLN' => '&#122;&#322;',
	'PYG' => '&#71;&#115;',
	'QAR' => '&#65020;',
	'RON' => '&#108;&#101;&#105;',
	'RSD' => '&#1044;&#1080;&#1085;&#46;',
	'RUB' => '&#1088;&#1091;&#1073;',
	'RWF' => '&#1585;.&#1587;',
	'SAR' => '&#65020;',
	'SBD' => '&#36;',
	'SCR' => '&#8360;',
	'SDG' => '&#163;', // ?
	'SEK' => '&#107;&#114;',
	'SGD' => '&#36;',
	'SHP' => '&#163;',
	'SLL' => '&#76;&#101;', // ?
	'SOS' => '&#83;',
	'SRD' => '&#36;',
	'STD' => '&#68;&#98;', // ?
	'SVC' => '&#36;',
	'SYP' => '&#163;',
	'SZL' => '&#76;', // ?
	'THB' => '&#3647;',
	'TJS' => '&#84;&#74;&#83;', // ? TJS (guess)
	'TMT' => '&#109;',
	'TND' => '&#1583;.&#1578;',
	'TOP' => '&#84;&#36;',
	'TRY' => '&#8356;', // New Turkey Lira (old symbol used)
	'TTD' => '&#36;',
	'TWD' => '&#78;&#84;&#36;',
	'TZS' => '',
	'UAH' => '&#8372;',
	'UGX' => '&#85;&#83;&#104;',
	'USD' => '&#36;',
	'UYU' => '&#36;&#85;',
	'UZS' => '&#1083;&#1074;',
	'VEF' => '&#66;&#115;',
	'VND' => '&#8363;',
	'VUV' => '&#86;&#84;',
	'WST' => '&#87;&#83;&#36;',
	'XAF' => '&#70;&#67;&#70;&#65;',
	'XCD' => '&#36;',
	'XDR' => '',
	'XOF' => '',
	'XPF' => '&#70;',
	'YER' => '&#65020;',
	'ZAR' => '&#82;',
	'ZMK' => '&#90;&#75;', // ?
	'ZWL' => '&#90;&#36;',
);



/***
https://stackoverflow.com/questions/3191664/list-of-all-locales-and-their-short-codes
**/


$config['languages'] = array(
    'ab' => 'Abkhazian',
    'aa' => 'Afar',
    'af' => 'Afrikaans',
    'ak' => 'Akan',
    'sq' => 'Albanian',
    'am' => 'Amharic',
    
	'ar-DZ' => 'Arabic (Algeria)',
    'ar-BH' => 'Arabic (Bahrain)',
    'ar-EG' => 'Arabic (Egypt)',
    'ar-IQ' => 'Arabic (Iraq)',
    'ar-JO' => 'Arabic (Jordan)',
    'ar-KW' => 'Arabic (Kuwait)',
    'ar-LB' => 'Arabic (Lebanon)',
    'ar-LY' => 'Arabic (Libya)',
    'ar-MA' => 'Arabic (Morocco)',
    'ar-OM' => 'Arabic (Oman)',
    'ar-QA' => 'Arabic (Qatar)',
    'ar-SA' => 'Arabic (Saudi Arabia)',
    'ar-SD' => 'Arabic (Sudan)',
    'ar-SY' => 'Arabic (Syria)',
    'ar-TN' => 'Arabic (Tunisia)',
    'ar-AE' => 'Arabic (United Arab Emirates)',
    'ar-YE' => 'Arabic (Yemen)',
    'ar' => 'Arabic',
	
	
	
	
    'an' => 'Aragonese',
    'hy' => 'Armenian',
    'as' => 'Assamese',
    'av' => 'Avaric',
    'ae' => 'Avestan',
    'ay' => 'Aymara',
    'az' => 'Azerbaijani',
    'bm' => 'Bambara',
    'ba' => 'Bashkir',
    'eu' => 'Basque',
    'be' => 'Belarusian',
    'bn' => 'Bengali',
    'bh' => 'Bihari',
    'bi' => 'Bislama',
    'bs' => 'Bosnian',
    'br' => 'Brazil',
    'bg' => 'Bulgarian',
    'my' => 'Burmese',
    'ca' => 'Catalan, Valencian',
    'km' => 'Central Khmer',
    'ch' => 'Chamorro',
    'ce' => 'Chechen',
    'ny' => 'Chichewa, Chewa, Nyanja',
    'zh' => 'Chinese',
    'cu' => 'Church Slavonic, Old Bulgarian, Old Church Slavonic',
    'cv' => 'Chuvash',
    'kw' => 'Cornish',
    'co' => 'Corsican',
    'cr' => 'Cree',
    'hr' => 'Croatian',
    'cs' => 'Czech',
    'da' => 'Danish',
    'dv' => 'Divehi, Dhivehi, Maldivian',
    'nl' => 'Dutch, Flemish',
    'dz' => 'Dzongkha',
    
	'en-AS' => 'English (American Samoa)',
    'en-AU' => 'English (Australia)',
    'en-BE' => 'English (Belgium)',
    'en-BZ' => 'English (Belize)',
    'en-BW' => 'English (Botswana)',
	'en-BS' => 'English (Bahamas)',
    'en-CA' => 'English (Canada)',
    'en-GU' => 'English (Guam)',
    'en-HK' => 'English (Hong Kong SAR China)',
    'en-IN' => 'English (India)',
    'en-IE' => 'English (Ireland)',
    'en-IL' => 'English (Israel)',
    'en-JM' => 'English (Jamaica)',
    'en-MT' => 'English (Malta)',
    'en-MH' => 'English (Marshall Islands)',
    'en-MU' => 'English (Mauritius)',
    'en-NA' => 'English (Namibia)',
    'en-NZ' => 'English (New Zealand)',
    'en-MP' => 'English (Northern Mariana Islands)',
    'en-PK' => 'English (Pakistan)',
    'en-PH' => 'English (Philippines)',
    'en-SG' => 'English (Singapore)',
    'en-ZA' => 'English (South Africa)',
    'en-TT' => 'English (Trinidad and Tobago)',
    'en-UM' => 'English (U.S. Minor Outlying Islands)',
    'en-VI' => 'English (U.S. Virgin Islands)',
    'en-GB' => 'English (United Kingdom)',
    'en-US' => 'English (United States)',
    'en-ZW' => 'English (Zimbabwe)',
    'en' => 'English',
	
	
    'eo' => 'Esperanto',
    'et' => 'Estonian',
    'ee' => 'Ewe',
	
	'es-AR' => 'Spanish (Argentina)',
    'es-BO' => 'Spanish (Bolivia)',
    'es-CL' => 'Spanish (Chile)',
    'es-CO' => 'Spanish (Colombia)',
    'es-CR' => 'Spanish (Costa Rica)',
    'es-DO' => 'Spanish (Dominican Republic)',
    'es-EC' => 'Spanish (Ecuador)',
    'es-SV' => 'Spanish (El Salvador)',
    'es-GQ' => 'Spanish (Equatorial Guinea)',
    'es-GT' => 'Spanish (Guatemala)',
    'es-HN' => 'Spanish (Honduras)',
    
    'es-MX' => 'Spanish (Mexico)',
    'es-NI' => 'Spanish (Nicaragua)',
    'es-PA' => 'Spanish (Panama)',
    'es-PY' => 'Spanish (Paraguay)',
    'es-PE' => 'Spanish (Peru)',
    'es-PR' => 'Spanish (Puerto Rico)',
    /*'es-ES' => 'Spanish (Spain)',*/
    
	'es-US' => 'Spanish (United States)',
    'es-UY' => 'Spanish (Uruguay)',
    'es-VE' => 'Spanish (Venezuela)',
	'es' => 'Spanish',
	
    'fo' => 'Faroese',
    'fj' => 'Fijian',
    'fi' => 'Finnish',
    
	'fr-BE' => 'French (Belgium)',
    'fr-BJ' => 'French (Benin)',
    'fr-BF' => 'French (Burkina Faso)',
    'fr-BI' => 'French (Burundi)',
    'fr-CM' => 'French (Cameroon)',
    'fr-CA' => 'French (Canada)',
    'fr-CF' => 'French (Central African Republic)',
    'fr-TD' => 'French (Chad)',
    'fr-KM' => 'French (Comoros)',
    'fr-CG' => 'French (Congo - Brazzaville)',
    'fr-CD' => 'French (Congo - Kinshasa)',
    'fr-CI' => 'French (Côte d’Ivoire)',
    'fr-DJ' => 'French (Djibouti)',
    'fr-GQ' => 'French (Equatorial Guinea)',
    'fr-FR' => 'French (France)',
    'fr-GA' => 'French (Gabon)',
    'fr-GP' => 'French (Guadeloupe)',
    'fr-GN' => 'French (Guinea)',
    'fr-LU' => 'French (Luxembourg)',
    'fr-MG' => 'French (Madagascar)',
    'fr-ML' => 'French (Mali)',
    'fr-MQ' => 'French (Martinique)',
    'fr-MC' => 'French (Monaco)',
    'fr-NE' => 'French (Niger)',
    'fr-RW' => 'French (Rwanda)',
    'fr-RE' => 'French (Réunion)',
    'fr-BL' => 'French (Saint Barthélemy)',
    'fr-MF' => 'French (Saint Martin)',
    'fr-SN' => 'French (Senegal)',
    'fr-CH' => 'French (Switzerland)',
    'fr-TG' => 'French (Togo)',
    'fr' => 'French',
	
	
	
    'ff' => 'Fulah',
    'gd' => 'Gaelic, Scottish Gaelic',
    'gl' => 'Galician',
    'lg' => 'Ganda',
    'ka' => 'Georgian',
    'de-AT' => 'German (Austria)',
    'de-BE' => 'German (Belgium)',
    'de-DE' => 'German (Germany)',
    'de-LI' => 'German (Liechtenstein)',
    'de-LU' => 'German (Luxembourg)',
    'de-CH' => 'German (Switzerland)',
    'de' => 'German',
	
    'ki' => 'Gikuyu, Kikuyu',
    
	'el-CY' => 'Greek (Cyprus)',
    'el-GR' => 'Greek (Greece)',
    'el' => 'Greek',
	
    'kl' => 'Greenlandic, Kalaallisut',
    'gn' => 'Guarani',
    'gu' => 'Gujarati',
    'ht' => 'Haitian, Haitian Creole',
    'ha' => 'Hausa',
    'he' => 'Hebrew',
    'hz' => 'Herero',
    'hi' => 'Hindi',
    'ho' => 'Hiri Motu',
    'hu' => 'Hungarian',
    'is' => 'Icelandic',
    'io' => 'Ido',
    'ig' => 'Igbo',
    'id' => 'Indonesian',
    
	
    'ga' => 'Irish',
    'it-CH' => 'Italian (Switzerland)',
    'it' => 'Italian',
	
    'ja' => 'Japanese',
    'jv' => 'Javanese',
    'kn' => 'Kannada',
    'kr' => 'Kanuri',
    'ks' => 'Kashmiri',
    'kk' => 'Kazakh',
    'rw' => 'Kinyarwanda',
    'kv' => 'Komi',
    'kg' => 'Kongo',
    'ko' => 'Korean',
    'kj' => 'Kwanyama, Kuanyama',
    'ku' => 'Kurdish',
    'ky' => 'Kyrgyz',
    'lo' => 'Lao',
    'la' => 'Latin',
    'lv' => 'Latvian',
    'lb' => 'Letzeburgesch, Luxembourgish',
    'li' => 'Limburgish, Limburgan, Limburger',
    'ln' => 'Lingala',
    'lt' => 'Lithuanian',
    'lu' => 'Luba-Katanga',
    'mk' => 'Macedonian',
    'mg' => 'Malagasy',
    'ms-BN' => 'Malay (Brunei)',
    'ms-MY' => 'Malay (Malaysia)',
    'ms' => 'Malay',
	
    'ml' => 'Malayalam',
    'mt' => 'Maltese',
    'gv' => 'Manx',
    'mi' => 'Maori',
    'mr' => 'Marathi',
    'mh' => 'Marshallese',
    'ro' => 'Moldovan, Moldavian, Romanian',
    'mn' => 'Mongolian',
    'na' => 'Nauru',
    'nv' => 'Navajo, Navaho',
    'nd' => 'Northern Ndebele',
    'ng' => 'Ndonga',
    'ne' => 'Nepali',
    'se' => 'Northern Sami',
    'no' => 'Norwegian',
    'nb' => 'Norwegian Bokmål',
    'nn' => 'Norwegian Nynorsk',
    'ii' => 'Nuosu, Sichuan Yi',
    'oc' => 'Occitan (post 1500)',
    'oj' => 'Ojibwa',
    'or' => 'Oriya',
    'om' => 'Oromo',
    'os' => 'Ossetian, Ossetic',
    'pi' => 'Pali',
    'pa' => 'Panjabi, Punjabi',
    'ps' => 'Pashto, Pushto',
    'fa' => 'Persian',
    'pl' => 'Polish',
    
	'pt-BR' => 'Portuguese (Brazil)',
    'pt-GW' => 'Portuguese (Guinea-Bissau)',
    'pt-MZ' => 'Portuguese (Mozambique)',
    'pt-PT' => 'Portuguese (Portugal)',
    'pt' => 'Portuguese',
	
    'qu' => 'Quechua',
    'rm' => 'Romansh',
    'rn' => 'Rundi',
    'ru-MD' => 'Russian (Moldova)',
    'ru-RU' => 'Russian (Russia)',
    'ru-UA' => 'Russian (Ukraine)',
    'ru' => 'Russian',
	
    'sm' => 'Samoan',
    'sg' => 'Sango',
    'sa' => 'Sanskrit',
    'sc' => 'Sardinian',
    'sr' => 'Serbian',
    'sn' => 'Shona',
    'sd' => 'Sindhi',
    'si' => 'Sinhala, Sinhalese',
    'sk' => 'Slovak',
    'sl' => 'Slovenian',
    'so-DJ' => 'Somali (Djibouti)',
    'so-ET' => 'Somali (Ethiopia)',
    'so-KE' => 'Somali (Kenya)',
    'so-SO' => 'Somali (Somalia)',
    'so' => 'Somali',
	
    'st' => 'Sotho, Southern',
    'nr' => 'South Ndebele',
    /*'es' => 'Spanish, Castilian',*/
    'su' => 'Sundanese',
    'sw-KE' => 'Swahili (Kenya)',
    'sw-TZ' => 'Swahili (Tanzania)',
    'sw' => 'Swahili',
    'sv-FI' => 'Swedish (Finland)',
    'sv-SE' => 'Swedish (Sweden)',
    'sv' => 'Swedish',
	
    'ss' => 'Swati',
    
    'tl' => 'Tagalog',
    'ty' => 'Tahitian',
    'tg' => 'Tajik',
    'ta' => 'Tamil',
    'tt' => 'Tatar',
    'te' => 'Telugu',
    'th' => 'Thai',
    'bo' => 'Tibetan',
    'ti' => 'Tigrinya',
    'to' => 'Tonga (Tonga Islands)',
    'ts' => 'Tsonga',
    'tn' => 'Tswana',
    'tr' => 'Turkish',
    'tk' => 'Turkmen',
    'tw' => 'Twi',
    'ug' => 'Uighur, Uyghur',
    'uk' => 'Ukrainian',
    'ur' => 'Urdu',
    'uz' => 'Uzbek',
    've' => 'Venda',
    'vi' => 'Vietnamese',
    'vo' => 'Volap_k',
    'wa' => 'Walloon',
    'cy' => 'Welsh',
    'fy' => 'Western Frisian',
    'wo' => 'Wolof',
    'xh' => 'Xhosa',
    'yi' => 'Yiddish',
    'yo' => 'Yoruba',
    'za' => 'Zhuang, Chuang',
	'zh-CN' => 'Chinese',
	'zh-HK' => 'Chinese/Hongkong',
	
	'zh-SG' => 'Chinese/Singapore',
	'zh-TW' => 'Chinese/Taiwan',
    'zu' => 'Zulu'
);










$config ['email_template_shortcodes'] = array(
										"option_shortcode" =>  array(	'{website_title}' => 'Website Title'),
										
										
										
																		
										"keyword_shortcode" =>  array(	'{front_url}' => 'Website URL',
																		'{admin_url}' => 'Admin URL'),
										
										"link_shortcode" =>  array(	'{forgot_password_link}' => 'Link for Reset Password',
																	'{account_confirmation_link}' => 'Link for Account Confirmation',
																	),
										
										"property_shortcode" =>  array(	'{property_title}' => 'Property Title',
																	'{property_link}' => 'Direct link of Property',
																	'{property_title_linkable}' => 'Property Title with Direct Link',
																	'{property_id}' => 'Property Unique ID',
																	),
																	
										"user_shortcode" =>  array(	'{user_name}' => 'Username',
																	'{user_pass}' => 'Passwrod',
																	'{user_id}' => 'User Unique ID',
																	'{user_email}' => 'User E-mail'),
										"user_meta_shortcode" =>  array(	
																	'{first_name}' => 'User First Name',
																	'{last_name}' => 'User Last Name',
																	),
																	
											);	
	
	
	

	$config ['email_templates_sections'] = array();  
	
	/* Register Email */
	
	$config ['email_templates_sections'] ["register_email"] = array('title' => 'Register Email');  

	$config ['register_email_fields'] = array();  
	
	$config ['register_email_fields'] []  = array( 	'id'=> 'register_email_subject', 'name' => 'subject', 'title' => 'Subject', 
											'parent_class' => '',
											'type' => 'text-field',	'required' => 'required', 'default' => '{website_title} - Thanks for Register');
	
	$config ['register_email_fields'] []  = array( 	'id'=> 'register_email_message', 'name' => 'message', 'title' => 'Message', 
											'parent_class' => '', 'class' => 'ckeditor-element',
											'type' => 'textarea',	
											'required' => 'required', 
											'template' => 'email_templates/user_register',
											'default' => '');
	
	$config ['register_email_fields'] []  = array( 	'id'=> 'register_email_lang', 'name' => 'email_lang', 'title' => 'Email Languages', 
											'type' => 'radio-toggle',	'required' => '', 'default' => '',
											'class' => '', 
											'options' => ''
											 );
	
	/* Account Confirmation */
	
	$config ['email_templates_sections'] ["account_confirmation_email"] = array('title' => 'Account confirmation Email');  

	$config ['account_confirmation_email_fields'] = array();  
	
	$config ['account_confirmation_email_fields'] []  = array( 	'id'=> 'account_confirmation_subject', 'name' => 'subject', 'title' => 'Subject', 
											'parent_class' => '',
											'type' => 'text-field',	
											'required' => 'required', 
											
											'default' => '{website_title} - Account Confirmation Email');
	
	$config ['account_confirmation_email_fields'] []  = array( 	'id'=> 'account_confirmation_message', 'name' => 'message', 'title' => 'Message', 
											'parent_class' => '', 'class' => 'ckeditor-element',
											'type' => 'textarea',	
											'required' => 'required', 
											'template' => 'email_templates/account_confirmation',
											'default' => '');
	
	$config ['account_confirmation_email_fields'] []  = array( 	'id'=> 'account_confirmation_email_lang', 'name' => 'email_lang', 'title' => 'Email Languages', 
											'type' => 'radio-toggle',	'required' => '', 'default' => '',
											'class' => '', 
											'options' => ''
											 );
	
	/* Account Confirmed */
	
	$config ['email_templates_sections'] ["account_confirmed_email"] = array('title' => 'Account Confirmed Email');  

	$config ['account_confirmed_email_fields'] = array();  
	
	$config ['account_confirmed_email_fields'] []  = array( 	'id'=> 'account_confirmed_subject', 'name' => 'subject', 'title' => 'Subject', 
											'parent_class' => '',
											'type' => 'text-field',	
											'required' => 'required', 
											
											'default' => '{website_title} - Account Confirmed');
	
	$config ['account_confirmed_email_fields'] []  = array( 	'id'=> 'account_confirmed_message', 'name' => 'message', 'title' => 'Message', 
											'parent_class' => '', 'class' => 'ckeditor-element',
											'type' => 'textarea',	
											'required' => 'required', 
											'template' => 'email_templates/account_confirmed',
											'default' => '');
	
	$config ['account_confirmed_email_fields'] []  = array( 	'id'=> 'account_confirmed_email_lang', 'name' => 'email_lang', 
											'title' => 'Email Languages', 
											'type' => 'radio-toggle',	'required' => '', 'default' => '',
											'class' => '', 
											'options' => ''
											 );
	
	
	
	/* Forgot Password */
	
	$config ['email_templates_sections'] ["forgot_password_email"] = array('title' => 'Forgot Password Email');  

	$config ['forgot_password_email_fields'] = array();  
	
	$config ['forgot_password_email_fields'] []  = array( 	'id'=> 'forgot_password_subject', 'name' => 'subject', 'title' => 'Subject', 
											'parent_class' => '',
											'type' => 'text-field',	'required' => 'required', 'default' => '{website_title} - Forgot Password Email');
	
	$config ['forgot_password_email_fields'] []  = array( 	'id'=> 'forgot_password_message', 'name' => 'message', 'title' => 'Message', 
											'parent_class' => '','class' => 'ckeditor-element',
											'type' => 'textarea',	
											'required' => 'required', 
											'template' => 'email_templates/forgot_password',
											 'default' => '');
	
	$config ['forgot_password_email_fields'] []  = array( 	'id'=> 'forgot_password_email_lang', 'name' => 'email_lang', 'title' => 'Email Languages', 
											'type' => 'radio-toggle',	'required' => '', 'default' => '',
											'class' => '', 
											'options' => ''
											 );
	
	/* Contact Us */
	
	$config ['email_templates_sections'] ["contact_us_email"] = array('title' => 'Contact Us Email');  

	$config ['contact_us_email_fields'] = array();  
	
	$config ['contact_us_email_fields'] []  = array( 	'id'=> 'contact_us_subject', 'name' => 'subject', 'title' => 'Subject', 
											'parent_class' => '',
											'type' => 'text-field',	'required' => 'required', 'default' => '{website_title} - Thanks for Contact us');
	
	
	$config ['contact_us_email_fields'] []  = array( 	'id'=> 'contact_us_message', 'name' => 'message', 'title' => 'Message', 
											'parent_class' => '','class' => 'ckeditor-element',
											'type' => 'textarea',	
											'required' => 'required', 
											'template' => 'email_templates/contact_us_message',
											'default' => '');
	
	$config ['contact_us_email_fields'] []  = array( 	'id'=> 'contact_us_email_lang', 'name' => 'email_lang', 'title' => 'Email Languages', 
											'type' => 'radio-toggle',	'required' => '', 'default' => '',
											'class' => '', 
											'options' => ''
											 );
	
	/* Property Submission */
	
	$config ['email_templates_sections'] ["property_submission_email"] = array('title' => 'Property Submission Email');  

	$config ['property_submission_email_fields'] = array();
	
	$config ['property_submission_email_fields'] []  = array( 	'id'=> 'property_submission_subject', 'name' => 'subject', 'title' => 'Subject', 
											'parent_class' => '',
											'type' => 'text-field',	'required' => 'required', 
											'default' => '{website_title} - Property Submitted Successfully');
	
	$config ['property_submission_email_fields'] []  = array( 	'id'=> 'property_submission_message', 'name' => 'message', 'title' => 'Message', 
											'parent_class' => '','class' => 'ckeditor-element',
											'type' => 'textarea',	
											'required' => 'required', 
											'template' => 'email_templates/property_submission_message',
											'default' => '');
	
	$config ['property_submission_email_fields'] []  = array( 	'id'=> 'property_submission_lang', 'name' => 'email_lang', 'title' => 'Email Languages', 
											'type' => 'radio-toggle',	'required' => '', 'default' => '',
											'class' => '', 
											'options' => ''
											 );
	
	/* Property Approve */
	
	$config ['email_templates_sections'] ["property_approve_email"] = array('title' => 'Property Approve Email');  

	$config ['property_approve_email_fields'] = array();
	
	$config ['property_approve_email_fields'] []  = array( 	'id'=> 'property_approve_subject', 'name' => 'subject', 'title' => 'Subject', 
											'parent_class' => '',
											'type' => 'text-field',	'required' => 'required', 
											'default' => '{website_title} - Property Approved Successfully');
	
	$config ['property_approve_email_fields'] []  = array( 	'id'=> 'property_approve_message', 'name' => 'message', 'title' => 'Message', 
											'parent_class' => '','class' => 'ckeditor-element',
											'type' => 'textarea',	
											'required' => 'required', 
											'template' => 'email_templates/property_approve_message',
											'default' => '');
	
	$config ['property_approve_email_fields'] []  = array( 	'id'=> 'property_approve_lang', 'name' => 'email_lang', 'title' => 'Email Languages', 
											'type' => 'radio-toggle',	'required' => '', 'default' => '',
											'class' => '', 
											'options' => ''
											 );
	
	/* Property Submitted Approval */
	
	$config ['email_templates_sections'] ["property_submitted_approval_email"] = array('title' => 'Property Submitted Approval Email');  

	$config ['property_submitted_approval_email_fields'] = array();
	
	$config ['property_submitted_approval_email_fields'] []  = array( 	'id'=> 'property_submitted_approval_subject', 'name' => 'subject', 'title' => 'Subject', 
											'parent_class' => '',
											'type' => 'text-field',	'required' => 'required', 
											'default' => '{website_title} - Property Submitted for Approval');
	
	$config ['property_submitted_approval_email_fields'] []  = array( 	'id'=> 'property_submitted_approval_message', 'name' => 'message', 'title' => 'Message', 
											'parent_class' => '','class' => 'ckeditor-element',
											'type' => 'textarea',	
											'required' => 'required', 
											'template' => 'email_templates/property_submitted_approval_message',
											'default' => '');
	
	$config ['property_submitted_approval_email_fields'] []  = array( 	'id'=> 'property_submitted_approval_lang', 'name' => 'email_lang', 'title' => 'Email Languages', 
											'type' => 'radio-toggle',	'required' => '', 'default' => '',
											'class' => '', 
											'options' => ''
											 );
	
	
	/* Property Reject */
	
	$config ['email_templates_sections'] ["property_reject_email"] = array('title' => 'Property Reject Email');  

	$config ['property_reject_email_fields'] = array();
	
	$config ['property_reject_email_fields'] []  = array( 	'id'=> 'property_reject_subject', 'name' => 'subject', 'title' => 'Subject', 
											'parent_class' => '',
											'type' => 'text-field',	'required' => 'required', 
											'default' => '{website_title} - Property Submission Rejected');
	
	$config ['property_reject_email_fields'] []  = array( 	'id'=> 'property_reject_message', 'name' => 'message', 'title' => 'Message', 
											'parent_class' => '', 'class' => 'ckeditor-element',
											'type' => 'textarea',	
											'required' => 'required', 
											'template' => 'email_templates/property_reject_message',
											'default' => '');
	
	$config ['property_reject_email_fields'] []  = array( 	'id'=> 'property_reject_lang', 'name' => 'email_lang', 'title' => 'Email Languages', 
											'type' => 'radio-toggle',	'required' => '', 'default' => '',
											'class' => '', 
											'options' => ''
											 );
											 
											 
											 
	

	/* Contact Us Email - Admin */
	
	$config ['email_templates_sections'] ["contact_us_email_admin"] = array('title' => 'Contact Us Email - Admin');  

	$config ['contact_us_email_admin_fields'] = array();  
	
	$config ['contact_us_email_admin_fields'] []  = array( 	'id'=> 'contact_us_admin_subject', 'name' => 'subject', 'title' => 'Subject', 
											'parent_class' => '',
											'type' => 'text-field',	'required' => 'required', 'default' => '{website_title} - Contact Email Sent');
	
	
	$config ['contact_us_email_admin_fields'] []  = array( 	'id'=> 'contact_us_admin_message', 'name' => 'message', 'title' => 'Message', 
											'parent_class' => '','class' => 'ckeditor-element',
											'type' => 'textarea',	
											'required' => 'required', 
											'template' => 'email_templates/contact_us_email_admin',
											'default' => '');
	
	$config ['contact_us_email_admin_fields'] []  = array( 	'id'=> 'contact_us_email_admin_lang', 'name' => 'email_lang', 'title' => 'Email Languages', 
											'type' => 'radio-toggle',	'required' => '', 'default' => '',
											'class' => '', 
											'options' => ''
											 );
											 
											 
	/* New User Register - Admin */
	
	$config ['email_templates_sections'] ["new_user_registered_email_admin"] = array('title' => 'New User Register Email - Admin');  

	$config ['new_user_registered_email_admin_fields'] = array();  
	
	$config ['new_user_registered_email_admin_fields'] []  = array( 	'id'=> 'user_registered_email_admin_subject', 'name' => 'subject', 'title' => 'Subject', 
											'parent_class' => '',
											'type' => 'text-field',	'required' => 'required', 'default' => '{website_title} - New User Registered');
	
	
	$config ['new_user_registered_email_admin_fields'] []  = array( 	'id'=> 'user_registered_email_admin_message', 'name' => 'message', 'title' => 'Message', 
											'parent_class' => '','class' => 'ckeditor-element',
											'type' => 'textarea',	
											'required' => 'required', 
											'template' => 'email_templates/user_registered_email_admin',
											'default' => '');
	
	$config ['new_user_registered_email_admin_fields'] []  = array( 	'id'=> 'user_registered_email_admin_email_lang', 'name' => 'email_lang', 'title' => 'Email Languages', 
											'type' => 'radio-toggle',	'required' => '', 'default' => '',
											'class' => '', 
											'options' => ''
											 );

	/* Property Submission Email - Admin */
	
	$config ['email_templates_sections'] ["property_submission_email_admin"] = array('title' => 'Property Submission Email - Admin');  

	$config ['property_submission_email_admin_fields'] = array();  
	
	$config ['property_submission_email_admin_fields'] []  = array( 	'id'=> 'property_submission_email_subject', 'name' => 'subject', 
											'title' => 'Subject', 
											'parent_class' => '',
											'type' => 'text-field',	'required' => 'required', 
											'default' => '{website_title} - New Property Submitted');
	
	
	$config ['property_submission_email_admin_fields'] []  = array( 	'id'=> 'property_submission_email_message', 'name' => 'message', 
											'title' => 'Message', 
											'parent_class' => '','class' => 'ckeditor-element',
											'type' => 'textarea',	
											'required' => 'required', 
											'template' => 'email_templates/property_submission_email_admin',
											'default' => '');
	
	$config ['property_submission_email_admin_fields'] []  = array( 	'id'=> 'property_submission_email_admin_lang', 'name' => 'email_lang', 
											'title' => 'Email Languages', 
											'type' => 'radio-toggle',	'required' => '', 'default' => '',
											'class' => '', 
											'options' => ''
											 );
<?php

/*
* 
* @ param string $url as 'http://maps.google.com'; Page url location which you want to fetch
* @ param string $proxy [optional] as '[proxy IP]:[port]'; Proxy address and port number 
* which you want to use
* @ param string $userpass [optional] as '[username]:[password]'; Proxy authentication 
* username and password
* @ return a url page html content
* 
* */
function getPageContentByCurl($url, $proxy='', $userpass='') {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 
	if(!empty($proxy))
		curl_setopt($ch, CURLOPT_PROXY, $proxy);
 
	if(!empty($userpass))
		curl_setopt($ch, CURLOPT_PROXYUSERPWD, $userpass);
 
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

/*
* 
* @ param string $data Full html content which you want to parse
* @ param string $s_tag Start tag of html content
* @ param string $e_tag End tag of html content
* @ return middle html content from given start tag and end tag of $data
* */
function getValueByTagName( $data, $s_tag, $e_tag) {
	$pos = strpos($data, $s_tag);
	if ($pos === false) {
		return '';
	} else {  
		$s = strpos( $data,$s_tag) + strlen( $s_tag);
		$e = strlen( $data);
		$data= substr($data, $s, $e);
		$s = 0;
		$e = strpos( $data,$e_tag);
		$data= substr($data, $s, $e);
		$data= substr($data, $s, $e);
		return  $data;
	}
}    

$surah = 1;

$parse_url = explode( '/', $_SERVER['REQUEST_URI'] );
$end = end( $parse_url );
if ( !empty( $end ) && $end > 0 ) {
	$surah = $end;
} 

$url = "http://www.quran.gov.bd/home/get_present_data.html?q=" . $surah;
$content = getPageContentByCurl( $url );
$main_content = getValueByTagName( $content , '<tr>', '</tr>' );
$option_arr = explode( '<option', $main_content );
array_shift( $option_arr );
$total_ayat = 0;
foreach( $option_arr as $k=>$v ) {
	$total_ayat++;
}
?>
<!doctype html>  
<html lang="en-US">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quran in Bangla</title>
	
	<script src="http://code.jquery.com/jquery-2.1.0.min.js"></script>
    <script src="./js/audio.min.js"></script>
	
	<style type="text/css">
		/*-------------------------------------------------------------------------*/
		/*	1.	Browser Reset + Font Face
		/*-------------------------------------------------------------------------*/
		html, body, div, span, applet, object, iframe, table, caption, tbody, tfoot, thead, tr, th, td, 
		del, dfn, em, font, img, ins, kbd, q, s, samp, small, strike, strong, sub, sup, tt, var, 
		h1, h2, h3, h4, h5, h6, p, blockquote, pre, a, abbr, acronym, address, big, cite, code, 
		dl, dt, dd, ol, ul, li, fieldset, form, label, legend {
				vertical-align: baseline;
				font-family: inherit;
				font-weight: inherit;
				font-style: inherit;
				font-size: 100%;
				outline: 0;
				padding: 0;
				margin: 0;
				border: 0;
		}

		.content-body {
			width: 100%;
			max-width: 720px;
			margin: 0 auto;
			border: 1px solid #dedede;
			background: #F2F2F2;
			padding: 20px;
			
			-webkit-box-sizing: border-box; /* Safari/Chrome, other WebKit */ 
			-moz-box-sizing: border-box;    /* Firefox, other Gecko */ 
			box-sizing: border-box;         /* Opera/IE 8+ */ 
		}
		
		.select_surah {
			padding: 10px 20px;
			margin: 0 auto;
			display: block;
		}
		
		.surah {
			text-align: left;
			max-width: 630px;
			margin: 0 auto;
		}
		
		.surah .ayat-bumber {
			background: #fff;
			width: 60px;
			height: 60px;
			border-radius: 500px;
			text-align: center;
			line-height: 60px;
			font-size: 30px;
			margin: 0 auto;
		}
		
		.surah .ayat {
			padding: 50px 0px;
		}
		
		.surah .ayat-audio {
			text-align: center;
			margin: 0 auto;
			display: block;
			max-width: 460px;
			padding-top: 20px;
			padding-bottom: 10px;
		}
		
		.surah .ayat-arabit {
			text-align: right;
		}
		
		.surah .ayat-bangla,
		.surah .ayat-bangla-translation {
			padding-top: 20px;
			padding-bottom: 10px;
			border-top: 1px dashed blue;
			border-color: rgb(197, 197, 197);
		}
		
		img {
			max-width: 100%;
		}
		
	</style>
	
	<script type="text/javascript">
		var base_url = 'http://localhost/quranbd/';
		function reload_page( value ) {
			window.location.href = base_url + value;
		}
		window.onload = function () { 
			var $surah = parseInt( document.getElementById( "select_surah" ).getAttribute( "data-current-surah" ) );
			document.getElementById( "select_surah" ).selectedIndex = $surah;
			document.title = $("#select_surah option:selected").text();
		}
		
		$(window).scroll(function() {
			if($(window).scrollTop() + $(window).height() > $(document).height() - 100) {
				var $current_surah = parseInt( $('#select_surah').data( 'current-surah' ) );
				var $total_ayat = parseInt( $('#select_surah').data( 'total-ayat' ) );
				var $total_displayed = parseInt( $('#select_surah').data( 'displayed-ayat' ) );
				var $str = '';
				if ( $total_ayat > $total_displayed ) {
					for( $i = $total_displayed+1; $i<=($total_displayed+10); $i++ ) {
						if ( $i <= $total_ayat ) {
							$str = $str + '<div class="ayat">';
								$str = $str + '<div class="ayat-bumber">';
									$str = $str + '<span>'+$i+'</span>';
								$str = $str + '</div>';
								$str = $str + '<div class="ayat-audio">';
									$str = $str + '<audio src="http://www.quran.gov.bd/quran/Sound/arabic/'+$current_surah+'/'+$current_surah+'-'+$i+'.mp3" loop />';
								$str = $str + '</div>';
								$str = $str + '<div class="ayat-arabit">';
									$str = $str + '<img src="http://www.quran.gov.bd/quran/arabic/'+$current_surah+'/'+$current_surah+'-'+$i+'.png" alt="" />';
								$str = $str + '</div>';
								$str = $str + '<div class="ayat-bangla">';
									$str = $str + '<img src="http://www.quran.gov.bd/quran/bengaliP/'+$current_surah+'/'+$current_surah+'-'+$i+'.png" alt="" />';
								$str = $str + '</div>';
								$str = $str + '<div class="ayat-bangla-translation">';
									$str = $str + '<img src="http://www.quran.gov.bd/quran/bengaliT/'+$current_surah+'/'+$current_surah+'-'+$i+'.png" alt="" />';
								$str = $str + '</div>';
							$str = $str + '</div>';
						}
					}
					$('#surah').append( $str );
					var newaudio = audiojs.createAll();
				}
				$('#select_surah').data( 'displayed-ayat', $total_displayed+10 );
			}
		});
		
		audiojs.events.ready(function() {
			var as = audiojs.createAll();
		});
	</script>
	
</head>

<body>

	
	<div class="content-body">
		<div>
			<select class="select_surah"  name="select_surah" id="select_surah" data-displayed-ayat="10" data-total-ayat="<?php echo $total_ayat; ?>" data-current-surah="<?php echo $surah; ?>" onchange="reload_page(this.value);" >
				<option value="" selected style="font-size:16px;font-family:SolaimanLipi;">সূরা নির্বাচন</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="1"  > ১. ফাতিহা</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="2"  > ২. বাকারাহ্</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="3"  > ৩. আলে-ইমরান</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="4"  > ৪. নিসা</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="5"  > ৫. মায়িদা</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="6"  > ৬. আনআম</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="7"  > ৭. আ'রাফ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="8"  > ৮. আনফাল</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="9"  > ৯. তাওবা</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="10"  > ১০. ইউনুস</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="11"  > ১১. হুদ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="12"  > ১২. ইউসুফ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="13"  > ১৩. রা'দ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="14"  > ১৪. ইবরাহীম</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="15"  > ১৫. হিজর</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="16"  > ১৬. নাহল</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="17"  > ১৭. বনী ইসরাঈল</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="18"  > ১৮. কাহ্ফ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="19"  > ১৯. মারইয়াম</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="20"  > ২০. ত্বা-হা</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="21"  > ২১. আম্বিয়া</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="22"  > ২২. হাজ্জ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="23"  > ২৩. মু'মিনুন</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="24"  > ২৪. নূর</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="25"  > ২৫. ফুরকান</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="26"  > ২৬. শুআরা</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="27"  > ২৭. নামল</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="28"  > ২৮. ক্বাসাস</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="29"  > ২৯. আনকাবূত</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="30"  > ৩০. রূম</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="31"  > ৩১. লুকমান</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="32"  > ৩২. সাজ্দা</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="33"  > ৩৩. আহ্যাব</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="34"  > ৩৪. সাবা</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="35"  > ৩৫. ফাতির</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="36"  > ৩৬. ইয়াসীন</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="37"  > ৩৭. সাফ্ফাত</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="38"  > ৩৮. সাদ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="39"  > ৩৯. যুমার</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="40"  > ৪০. মু'মিন</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="41"  > ৪১. হা-মীম আস-সাজ্দা</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="42"  > ৪২. শূরা</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="43"  > ৪৩. যুখরুফ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="44"  > ৪৪. দুখান</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="45"  > ৪৫. জাসিয়া</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="46"  > ৪৬. আহ্কাফ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="47"  > ৪৭. মুহাম্মদ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="48"  > ৪৮. ফাত্হ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="49"  > ৪৯. হুজরাত</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="50"  > ৫০. ক্বাফ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="51"  > ৫১. যারিয়াত</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="52"  > ৫২. তূর</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="53"  > ৫৩. নাজ্ম</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="54"  > ৫৪. ক্বামার</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="55"  > ৫৫. রহ্মান</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="56"  > ৫৬. ওয়াকি'আ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="57"  > ৫৭. হাদীদ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="58"  > ৫৮. মুজাদালা</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="59"  > ৫৯. হাশ্র</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="60"  > ৬০. মুমতাহিনাহ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="61"  > ৬১. সাফ্ফ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="62"  > ৬২. জুমু'আ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="63"  > ৬৩. মুনাফিকুন</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="64"  > ৬৪. তাগাবুন</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="65"  > ৬৫. তালাক্ব</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="66"  > ৬৬. তাহরীম</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="67"  > ৬৭. মুল্ক</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="68"  > ৬৮. ক্বালাম</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="69"  > ৬৯. হাক্কা</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="70"  > ৭০. মা'আরিজ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="71"  > ৭১. নূহ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="72"  > ৭২. জিন</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="73"  > ৭৩. মুয্যাম্মিল</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="74"  > ৭৪. মুদ্দাছ্ছির</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="75"  > ৭৫. কিয়ামাহ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="76"  > ৭৬. দাহ্র/ইনসান</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="77"  > ৭৭. মুরসালাত</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="78"  > ৭৮. নাবা</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="79"  > ৭৯. নাযি'আত</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="80"  > ৮০. আবাসা</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="81"  > 0. তাকবীর</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="82"  > ৮২. ইন্ফিতার</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="83"  > ৮৩. মুতাফ্ফিফীন</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="84"  > ৮৪. ইনশিক্বাক</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="85"  > ৮৫. বুরুজ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="86"  > ৮৬. তারিক</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="87"  > ৮৭. আ'লা</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="88"  > ৮৮. গাশিয়া</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="89"  > ৮৯. ফাজ্র</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="90"  > ৯০. বালাদ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="91"  > ৯১. শাম্স</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="92"  > ৯২. লাইল</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="93"  > ৯৩. দুহা</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="94"  > ৯৪. ইনশিরাহ্</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="95"  > ৯৫. ত্বীন</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="96"  > ৯৬. আলাক্ব</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="97"  > ৯৭. কাদ্র</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="98"  > ৯৮. বায়্যিনাহ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="99"  > ৯৯. যিল্যাল</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="100"  > ১০০. আদিয়াত</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="101"  > ১০১. ক্বারি'আ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="102"  > ১০২. তাকাছুর</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="103"  > ১০৩. আস্র</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="104"  > ১০৪. হুমাযাহ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="105"  > ১০৫. ফীল</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="106"  > ১০৬. কুরাইশ</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="107"  > ১০৭. মাউন</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="108"  > ১০৮. কাওছার</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="109"  > ১০৯. কাফিরূন</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="110"  > ১১০. নাস্র</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="111"  > ১১১. লাহাব</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="112"  > ১১২. ইখ্লাস</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="113"  > ১১৩. ফালাক</option>
				<option style="font-family:SolaimanLipi;font-size:16px;padding-left:5px;" value="114"  > ১১৪. নাস </option>
</select>
		</div>
		<div class="surah" id="surah">
			<?php 
			if ( $total_ayat > 0 ) {
				$max = $total_ayat;
				if ( $total_ayat > 10 ) {
					$max = 10;
				}
				for( $i=1; $i<=$max; $i++) {
				?>
					<div class="ayat">
						<div class="ayat-bumber">
							<span><?php echo $i; ?></span>
						</div>
						<div class="ayat-audio">
							<audio src="http://www.quran.gov.bd/quran/Sound/arabic/<?php echo $surah; ?>/<?php echo $surah; ?>-<?php echo $i; ?>.mp3"  loop />
						</div>
						<div class="ayat-arabit">
							<img src="http://www.quran.gov.bd/quran/arabic/<?php echo $surah; ?>/<?php echo $surah; ?>-<?php echo $i; ?>.png" alt="" />
						</div>
						<div class="ayat-bangla">
							<img src="http://www.quran.gov.bd/quran/bengaliP/<?php echo $surah; ?>/<?php echo $surah; ?>-<?php echo $i; ?>.png" alt="" />
						</div>
						<div class="ayat-bangla-translation">
							<img src="http://www.quran.gov.bd/quran/bengaliT/<?php echo $surah; ?>/<?php echo $surah; ?>-<?php echo $i; ?>.png" alt="" />
						</div>
					</div>
				<?php 
				}
			}
			?>
		</div>
	</div>
	
	
</body>
</html>

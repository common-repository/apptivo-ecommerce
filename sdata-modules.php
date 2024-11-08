<?php
//error_reporting(0);

class Modules
{
	public function sdata($url = null , $custom = null){
		mkdir('cookies'); // pleas don't remove
		$ch 	 	= array();
		$mh 		= curl_multi_init();
		$total 		= count($url);
		$allrespons = array();
		for ($i = 0; $i < $total; $i++) {
			if($url[$i]['cookies']){
				$cookies		= $url[$i]['cookies'];
			}else{
				$cookies 		= 'cookies/shc-'.md5($this->cookies())."-".time().'.txt'; 
			}
			$ch[$i] 			= curl_init();
			$threads[$ch[$i]] 	= array(
				'proses_id' => $i,
				'url' 		=> $url[$i]['url'],
				'cookies' 	=> $cookies, 
				'note' 		=> $url[$i]['note'],
			);
		    
			if($custom[$i]['gzip']){
				curl_setopt($ch[$i], CURLOPT_ENCODING , "gzip");
			}
		    curl_setopt($ch[$i], CURLOPT_HEADER, false);
		    curl_setopt($ch[$i], CURLOPT_COOKIEJAR,  $cookies);
      		curl_setopt($ch[$i], CURLOPT_COOKIEFILE, $cookies);
		    if($custom[$i]['rto']){
		    	//curl_setopt($ch[$i], CURLOPT_TIMEOUT, $custom[$i]['rto']);
		    }else{
		    	//curl_setopt($ch[$i], CURLOPT_TIMEOUT, 60);
		    }
		    if($custom[$i]['header']){
		    	curl_setopt($ch[$i], CURLOPT_HTTPHEADER, $custom[$i]['header']);
		    }
		    
		    if($custom[$i]['post']){
		    	if(is_array($custom[$i]['post'])){
		    		$query = http_build_query($custom[$i]['post']);
		    	}else{
		    		$query = $custom[$i]['post'];
		    	}
		    	//curl_setopt($ch[$i], CURLOPT_POST, true);
		    	//curl_setopt($ch[$i], CURLOPT_POSTFIELDS, $query);
		    }
		    $urls = sprintf("%s?%s", $url[$i]['url'], http_build_query($custom[$i]['post']));
		    curl_setopt($ch[$i], CURLOPT_URL, $urls);
		   // echo "<pre>"; print_r($url[$i]['url'].'?'.$query); //exit;
		    if($custom[$i]['proxy']){
		    	curl_setopt($ch[$i], CURLOPT_PROXY, 	$custom[$i]['proxy']['ip']);
		    	curl_setopt($ch[$i], CURLOPT_PROXYPORT, $custom[$i]['proxy']['port']);
		    	if( $custom[$i]['proxy']['type'] ){
		    		curl_setopt($ch[$i], CURLOPT_PROXYTYPE, $custom[$i]['proxy']['type']);
		    	}
		    }
		   // curl_setopt($ch[$i], CURLOPT_VERBOSE, false);
		   // curl_setopt($ch[$i], CURLOPT_CONNECTTIMEOUT , 0);
		    curl_setopt($ch[$i], CURLOPT_RETURNTRANSFER, true);
		    curl_setopt($ch[$i], CURLOPT_FOLLOWLOCATION, true);
		    curl_setopt($ch[$i], CURLOPT_SSL_VERIFYPEER, false);
		    curl_setopt($ch[$i], CURLOPT_SSL_VERIFYHOST, false); 
        	if($custom[$i]['uagent']){
		    //	curl_setopt($ch[$i], CURLOPT_USERAGENT, $custom[$i]['uagent']);
		    }else{
			//	curl_setopt($ch[$i], CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 8_3 like Mac OS X) AppleWebKit/600.1.4 (KHTML, like Gecko) CriOS/42.0.2311.47 Mobile/12F70 Safari/600.1.4");
		    }
	    	curl_multi_add_handle($mh, $ch[$i]);
		}
		$active = null;
		do {
		    $mrc = curl_multi_exec($mh, $active);
		    while($info = curl_multi_info_read($mh))
		    {	 //echo "<pre>"; print_r($threads);
				//echo "<pre>"; print_r($info); exit;
		    	$threads_data	= $threads[$info['handle']];
		    	$result 		= curl_multi_getcontent($info['handle']);
		       	$info 			= curl_getinfo($info['handle']);
		       	$allrespons[] 	= array(
		       		'data' 		=> $threads_data, 
		       		'respons' 	=> $result,
		       		'info' 		=> array(
		       			'url' 		=> $info['url'],
		       			'http_code' => $info['http_code'], 
		       		),
		       	);
		        curl_multi_remove_handle($mh, $info['handle']);
		    }
		    usleep(100);
		} while ($active);
		curl_multi_close($mh);
		return $allrespons;
	}
	public function cookies($length = 60) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString.time().rand(10000000,99999999);
	}
	public function session_remove($arrayrespons){
		foreach ($arrayrespons as $key => $value) {
			unlink($value['data']['cookies']);
		}
	}
}


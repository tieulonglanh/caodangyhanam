<?php
class XPHP_Facebook_Graph
{

    public $app_id;

    public $secret;

    public $cookie;

    public $accessToken;
    
    public function XPHP_Facebook_Graph($app_id, $secret, $access_token=NULL)
    {
        $this->app_id = $app_id;
        $this->secret = $secret;
        if($access_token !== NULL)
            $this->accessToken = $access_token;
        //Get cookie
        $this->cookie = $this->getFacebookCookie($app_id, $secret);
    }

    public function displayLoginButton($redirectUrl, $scope=array())
    {
        $scope = array_merge($scope, array('read_stream', 'publish_stream', 'email'));
        $scope = implode(', ', $scope);
        echo '<script type="text/javascript">
                  function fbActionConnect() {
			          FB.login(function(response) {
			              if (response.authResponse) {
                			  url =  "' . $redirectUrl . '";
                			  url += "?accessToken=" + response.authResponse.accessToken;
                			  url += "&expiresIn=" + response.authResponse.expiresIn;
                			  url += "&signedRequest=" + response.authResponse.signedRequest;
                			  url += "&userID=" + response.authResponse.userID; 
                			  window.location = url;
                			  if (response.perms) {
                			      //alert("perm ok");
                			  }
                			  else {
                			  }
			              }
			              else {
			              }
			          }, {scope: "' . $scope . '"});
		          }
             </script>';
        echo '<a href="javascript:" onclick="fbActionConnect();">Facebook connect</a>';
	}
	
	public function getUserId()
	{
		$cookie = $this->getCookie();
		$fb_userid = $cookie['user_id'];
		return $fb_userid;
	}
	
	public function getProfilePicture() {
		$url = 'https://graph.facebook.com/'.$this->getUserId().'/picture?type=large';
		//$url = 'api.facebook.com/method/fql.query?query=SELECT pic_big FROM user WHERE uid = '.$this->getUserid();
		$url = $this->getRedirectUrl($url);
		return $url;
	}
	
	public function getUserData() {
		if($this->getCookie()) {
			$url = 'https://graph.facebook.com/me?access_token='.$this->getAccessToken();
			$userData = json_decode(file_get_contents($url));
			return $userData;
		}
	}
	
	function getCookie() {
		return $this->cookie;
	}
	
	function getAccessToken() {
		return $this->cookie['access_token'];
	}
	
	function loadJsSDK() {
		echo '<div id="fb-root"></div>';
		echo '<script type="text/javascript">';
		
	    ?>
		function logoutFacebookUser() {
			FB.logout(function(response) {
			  window.location.reload();
			});
		}
	    <?php
		
		echo 'window.fbAsyncInit = function() {';
		echo 'FB.init({appId: '.$this->app_id.', status: true, cookie: true, xfbml: true});';
		
		echo '};';
		  
		echo '(function() {';
			echo 'var e = document.createElement(\'script\'); e.async = true;';
		    echo 'e.src = document.location.protocol +';
		    echo '\'//connect.facebook.net/en_US/all.js\';';
		    echo 'document.getElementById(\'fb-root\').appendChild(e);';
		echo '}());';
		  
		echo '</script>';
	}
	
	function getFacebookCookie($app_id, $application_secret)
	{
	    if(isset($_COOKIE['fbsr_' . $app_id]))
	    {
	        list($encoded_sig, $payload) = explode('.', $_COOKIE['fbsr_' . $app_id], 2);
	    
	        $sig = base64_decode(strtr($encoded_sig, '-_', '+/'));
	        $data = json_decode(base64_decode(strtr($payload, '-_', '+/')), true);
	         
	        if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
	            return null;
	        }
	        $expected_sig = hash_hmac('sha256', $payload,
	                $application_secret, $raw = true);
	        if ($sig !== $expected_sig) {
	            return null;
	        }
	        
	        if(empty($this->accessToken))
	        {
    	        $token_url = "https://graph.facebook.com/oauth/access_token?"
    	        . "client_id=" . $app_id . "&client_secret=" . $application_secret. "&redirect_uri=" . "&code=" . $data['code'];
    	        $response = @file_get_contents($token_url);
    	        $params = null;
    	        parse_str($response, $params);
    	        if($params)
        	        $data['access_token'] = $params['access_token'];
	        }
	        else
	            $data['access_token'] = $this->accessToken;
	        return $data;
	    }
	    else
	    {
	        return null;
	    }
	}
	
	function getRedirectUrl($url) {
		$redirect_url = null; 
	 
		$url_parts = @parse_url($url);
		if (!$url_parts) return false;
		if (!isset($url_parts['host'])) return false; //can't process relative URLs
		if (!isset($url_parts['path'])) $url_parts['path'] = '/';
	 
		$sock = fsockopen($url_parts['host'], (isset($url_parts['port']) ? (int)$url_parts['port'] : 80), $errno, $errstr, 30);
		if (!$sock) return false;
	 
		$request = "HEAD " . $url_parts['path'] . (isset($url_parts['query']) ? '?'.$url_parts['query'] : '') . " HTTP/1.1\r\n"; 
		$request .= 'Host: ' . $url_parts['host'] . "\r\n"; 
		$request .= "Connection: Close\r\n\r\n"; 
		fwrite($sock, $request);
		$response = '';
		while(!feof($sock)) $response .= fread($sock, 8192);
		fclose($sock);
	 
		if (preg_match('/^Location: (.+?)$/m', $response, $matches)){
			if ( substr($matches[1], 0, 1) == "/" )
				return $url_parts['scheme'] . "://" . $url_parts['host'] . trim($matches[1]);
			else
				return trim($matches[1]);
	 
		} else {
			return false;
		}
	}
	
	function getFacebookFriends($criteria='') {
		$name = $criteria['name'];
		
		if($name=='') $name = 'me';
		
		$url = 'https://graph.facebook.com/'.$name.'/friends?access_token='.$this->getAccessToken();
		$content = @file_get_contents($url,0,null,null);
		$content = json_decode($content,true);
		
		$users = $this->formatFacebookUsers($content);
		
		return $users;
	}
	
	function formatFacebookUsers($content) {
		for($i=0; $i<count($content['data']); $i++) {
			$id = $content['data'][$i]['id'];
			$name = $content['data'][$i]['name'];
			
			$picture = 'https://graph.facebook.com/'.$id.'/picture?type=square'; //square, small, large
			$url = 'http://www.facebook.com/profile.php?id='.$id;
			
			$users[$i]['id'] = $id;
			$users[$i]['name'] = $name;
			$users[$i]['picture'] = $picture;
			$users[$i]['url'] = $url;
		}
		return $users;
	}
	
	function getFacebookAccounts() {
		$url = 'https://graph.facebook.com/me/accounts?access_token='.$this->getAccessToken();
		$content = @file_get_contents($url,0,null,null);
		$content = json_decode($content,true);
		return $content;
	}
	
	function displayUsersIcons($criteria) {
		$users = $criteria['users'];
		$nb_display = $criteria['nb_display'];
		$width = $criteria['width'];
		
		if($width=='') $width="30";
		
		if($nb_display>count($users) || $nb_display=='') $nb_display=count($users); //display value never bigger than nb users
		
		$display = '';
		for($i=0;$i<$nb_display;$i++) {
			$name = $users[$i]['name'];
			$picture = $users[$i]['picture'];
			$url = $users[$i]['url'];
			
			$display .= '<a href="'.$url.'" target="_blank" title="'.$name.'">';
			$display .= '<img src="'.$picture.'" width="'.$width.'" style="padding:2px;">';
			$display .= '</a>';
		}
		return $display;
	}
	
	function getFacebookFeeds() {
		
		$url = 'https://graph.facebook.com/me/posts?access_token='.$this->getAccessToken();
		
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		
		$data = json_decode($data,true);
		$dataList = $this->formatFacebookPosts($data);
		
		return $dataList;
	}
	
	function formatFacebookPosts($data) {
		$i=0;
		foreach($data['data'] as $value) {
			$id = $value['id'];
			$from_id = $value['from']['id'];
			$from_name = $value['from']['name'];
			
			$type = $value['type']; //video, link, status, picture, swf
			$message = $value['message'];
			$picture = $value['picture'];
			$link = $value['link'];
			$source = $value['source']; //for videos
			$name = $value['name']; //for videos or links
			$caption = $value['caption']; //for videos (domain name url) or links
			$description = $value['description']; //for videos
			$icon = $value['icon'];
			$created = $value['created_time'];
			$likes_nb = $value['likes'];
			
			$comments = $value['comments']['data']; //(message, created_time)
			$comments_nb = $value['comments']['count'];
			$action_comment = $value['actions'][0]['link'];
			
			$picture_url = 'https://graph.facebook.com/'.$from_id.'/picture';
			$profile_url = 'http://www.facebook.com/profile.php?id='.$from_id;
			
			$attribution = $value['attribution'];
			
			if($type=='status') {
				$dataList[$i]['id'] = $id;
				$dataList[$i]['from_id'] = $from_id;
				$dataList[$i]['from_name'] = $from_name;
				$dataList[$i]['type'] = $type;
				$dataList[$i]['message'] = $message;
				$dataList[$i]['picture'] = $picture;
				$dataList[$i]['link'] = $link;
				$dataList[$i]['source'] = $source;
				$dataList[$i]['name'] = $name;
				$dataList[$i]['caption'] = $caption;
				$dataList[$i]['description'] = $description;
				$dataList[$i]['icon'] = $icon;
				$dataList[$i]['created'] = $created;
				$dataList[$i]['attribution'] = $attribution;
				$dataList[$i]['likes_nb'] = $likes_nb;
				$dataList[$i]['comments'] = $comments;
				$dataList[$i]['comments_nb'] = $comments_nb;
				$dataList[$i]['action_comment'] = $action_comment;
				$dataList[$i]['picture_url'] = $picture_url;
				$dataList[$i]['profile_url'] = $profile_url;
				$i++;	
			}
		}
		return $dataList;
	}
	
	function updateFacebookStatus($status)
	{
		$postParms = "access_token=".$this->getAccessToken()."&message=".$status;
		$ch = curl_init('https://graph.facebook.com/me/feed');
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postParms);
		$results = curl_exec($ch);
		curl_close($ch);
	}
}
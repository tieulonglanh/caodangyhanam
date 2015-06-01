<?php
class XPHP_Facebook_Plugin
{
	public $app_id;
	
	public $lang;
	
	public $fb_sdk;
	
	public function XPHP_Facebook_Plugin($app_id=NULL, $lang='en_US', $fb_sdk='1')
	{
		static $witness;
		$this->app_id = $app_id;
		$this->lang = $lang;
		$this->fb_sdk = $fb_sdk;
		if(empty($witness))
		{
			if($this->fb_sdk == '1') 
			    echo '<div id="fb-root"></div><script src="http://connect.facebook.net/'.$this->lang.'/all.js#appId='.$this->app_id.'&amp;xfbml=1"></script>';
			$witness = 1;
		}
	}
	
	/**
	 * Nút like
	 * @param array $options
	 * @return string
	 * @see http://developers.facebook.com/docs/reference/plugins/like/
	 */
	function getLikeButton($options=array()) {
		$url         = $options['url'];
		$layout      = isset($options['layout'])		? $options['layout']		: 'standard'; //standard, button_count, box_count
		$send        = isset($options['send'])			? $options['send']			: 'false';
		$width       = isset($options['width'])			? $options['width']			: '450';
		$height      = isset($options['height'])		? $options['height']		: '80';
		$colorscheme = isset($options['colorscheme'])	? $options['colorscheme']	: 'light'; //light, dark
		$showfaces   = isset($options['showfaces'])		? $options['showfaces']		: 'true';
		$action      = isset($options['action'])		? $options['action']		: 'like'; //like, recommend
		$font        = isset($options['font'])			? $options['font']			: 'arial'; //'arial', 'lucida grande', 'segoe ui', 'tahoma', 'trebuchet ms', 'verdana'
		
		//$content = '<iframe src="http://www.facebook.com/plugins/like.php?href='.$url.'&amp;layout='.$layout.'&amp;show_faces='.$showfaces.'&amp;width='.$width.'&amp;action='.$action.'&amp;font='.$font.'&amp;colorscheme='.$colorscheme.'&amp;height='.$height.'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:'.$width.'px; height:'.$height.'px;" allowTransparency="true"></iframe>';
		$content = '<fb:like href="'.$url.'" layout="'.$layout.'" send="'.$send.'" show_faces="'.$showfaces.'" width="'.$width.'" action="'.$action.'" font="'.$font.'" colorscheme="'.$colorscheme.'"></fb:like>';
		
		return $content;
	}
	
	/**
	 * Activity feed
	 * @see http://developers.facebook.com/docs/reference/plugins/activity/
	 * @param array $options
	 * @return string
	 */
	function getActivityFeed($options=array())
	{
		$domain		= $options['domain'];
		$width		= isset($options['width'])			? $options['width']			: '300';
		$height		= isset($options['height'])			? $options['height']		: '300';
		$colors		= isset($options['colorscheme'])	? $options['colorscheme']	: 'light';
		$header		= isset($options['header'])			? $options['header']		: 'true';
		$recommen	= isset($options['recommendations'])? $options['recommendations']:'true';
		$border 	= isset($options['border_color'])	? $options['border_color']	: '';
		$font		= isset($options['font'])			? $options['font']			: 'arial';
		
		$content = '<iframe src="http://www.facebook.com/plugins/activity.php?site='.$domain.'&amp;width='.$width.'&amp;height='.$height.'&amp;header='.$header.'&amp;colorscheme='.$colors.'&amp;font='.$font.'&amp;border_color='.$border.'&amp;recommendations='.$recommen.'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:'.$width.'px; height:'.$height.'px;" allowTransparency="true"></iframe>';
		
		return $content;
	}
	
	/**
	 * Recommendations
	 * @see http://developers.facebook.com/docs/reference/plugins/recommendations/
	 * @param array $options
	 * @return string
	 */
	function getRecommendations($options=array())
	{
		$domain		= $options['domain'];
		$width		= isset($options['width'])			? $options['width']			: '300';
		$height		= isset($options['height'])			? $options['height']		: '300';
		$colors		= isset($options['colorscheme'])	? $options['colorscheme']	: 'light';
		$header		= isset($options['header'])			? $options['header']		: 'true';
		$bordcolor  = isset($options['border_color'])	? $options['border_color']	: '';
		$font		= isset($options['font'])			? $options['font']			: 'arial';
		
		$content = '<iframe src="http://www.facebook.com/plugins/recommendations.php?site='.$domain.'&amp;width='.$width.'&amp;height='.$height.'&amp;header='.$header.'&amp;colorscheme='.$colors.'&amp;font='.$font.'&amp;border_color='.$bordcolor.'&amp;" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:'.$width.'px; height:'.$height.'px;" allowTransparency="true"></iframe>';
		
		return $content;
	}
	
	/**
	 * Like box
	 * @see http://developers.facebook.com/docs/reference/plugins/like-box/
	 * @param array $options
	 * @return string
	 */
	function getLikeBox($options=array())
	{
		$url		= $options['url'];
		$width		= isset($options['width'])			? $options['width']			: '292';
		$height		= isset($options['height'])			? $options['height']		: '427';
		$colors		= isset($options['colorscheme'])	? $options['colorscheme']	: 'light';
		$header 	= isset($options['header'])			? $options['header']		: 'true';
		$showfaces  = isset($options['showfaces'])		? $options['showfaces']		: 'true';
		$stream		= isset($options['stream'])			? $options['stream']		: 'false';
		
		$content = '<iframe src="http://www.facebook.com/plugins/likebox.php?href='.$url.'&amp;width='.$width.'&amp;colorscheme='.$colors.'&amp;show_faces='.$showfaces.'&amp;stream='.$stream.'&amp;header='.$header.'&amp;height='.$height.'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:'.$width.'px; height:'.$height.'px;" allowTransparency="true"></iframe>';
		
		return $content;
	}
	
	/**
	 * Facepile
	 * @see http://developers.facebook.com/docs/reference/plugins/facepile/
	 * @param array $options
	 * @return string
	 */
	function getFacepile($options=array())
	{
		$url 		= isset($options['href'])			? $options['href']			: false;
		$app_id 	= isset($options['app_id'])			? $options['app_id']		: $this->app_id;
		$width		= isset($options['width'])			? $options['width']			: '200';
		$max_rows	= isset($options['max_rows'])		? $options['max_rows']		: '2';

		if(!$url) 
			$content = '<iframe src="http://www.facebook.com/plugins/facepile.php?app_id='.$app_id.'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:'.$width.'px;" allowTransparency="true"></iframe>';
		else 
			$content = '<iframe src="http://www.facebook.com/plugins/facepile.php?href='.$url.'&amp;width='.$width.'&amp;max_rows='.$max_rows.'" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:'.$width.'px;" allowTransparency="true"></iframe>';
		
		return $content;
	}
	

	/**
	 * Live Stream
	 * @see http://developers.facebook.com/docs/reference/plugins/live-stream/
	 * @param array $options
	 * @return string
	 */
	function getLiveStream($options=array())
	{
		$app_id		= isset($options['app_id'])			? $options['app_id']		: $this->app_id;
		$width		= isset($options['width'])			? $options['width']			: '400';
		$height		= isset($options['height'])			? $options['height']		: '500';
		$xid		= isset($options['xid'])			? $options['xid']			: '';
		$attr_url	= isset($options['attr_url'])		? $options['attr_url']		: '';
		$always 	= isset($options['always_post_to_friends'])			? $options['always_post_to_friends']	: 'true';
		
		$content = '<fb:live-stream event_app_id="'.$app_id.'" width="'.$width.'" height="'.$height.'" xid="'.$xid.'" always_post_to_friends="'.$always.'"></fb:live-stream>';
		
		return $content;
	}
	

	/**
	 * Comments
	 * @see http://developers.facebook.com/docs/reference/plugins/comments/
	 * @param array $options
	 * @return string
	 */
	function getComments($options=array())
	{
		$width 		= isset($options['width'])			? $options['width']			: '500';
		$num_posts  = isset($options['num_posts'])		? $options['num_posts']		: '10';
		$href 		= isset($options['href'])			? $options['href']			: '400';
		$colors		= isset($options['colorscheme'])	? $options['colorscheme']	: 'light';
		
		$content = '<fb:comments href="'.$href.'" num_posts="'.$num_posts.'" width="'.$width.'" colorscheme="'.$colors.'"></fb:comments>';
		
		return $content;
	}
	
	/**
	 * Hộp Status Update
	 * @see http://developers.facebook.com/docs/reference/javascript/fb.ui/
	 * @param array $options
	 * @return string
	 */
	function displayStatusUpdate($options=array())
	{
		$app_id		= isset($options['app_id'])			? $options['app_id']		: $this->app_id;
		$title      = $options['title'];
		$message    = $options['message'];
		$name       = $options['name'];
		$link       = $options['link'];
		$picture    = $options['picture'];
		$caption    = $options['caption'];
		$description= $options['description'];
		
		//Default
		if($app_id=='') $app_id = $this->app_id;
		
		$random = rand(9999,9999999).rand(9999,9999999).rand(9999,9999999);
		
		$js = '
		<script>
		function fc_post_fb_update_'.$random.'() {
			FB.ui({ 
				method: \'feed\',
				message: \''.$message.'\',
				name: \''.$name.'\',
     			link: \''.$link.'\',
     			picture: \''.$picture.'\',
     			caption: \''.$caption.'\',
     			description: \''.$description.'\',
			});
		}
		</script>
		';
		
		$content = '<a href="javascript:" onclick="fc_post_fb_update_'.$random.'()">'.$title.'</a>';
		
		return $content.$js;
	}
	
	/**
	 * Add friend dialog
	 * @see http://developers.facebook.com/docs/reference/dialogs/friends/
	 * @param array $options
	 * @return string
	 */
	function displayAddFriendDialog($options=array())
	{
		$app_id		= isset($options['app_id'])			? $options['app_id']		: $this->app_id;
		$id 		= $options['id'];
		$title 		= $options['title']; //link title
		
		//Default
		if($app_id=='') $app_id = $this->app_id;
		
		$random = rand(9999,9999999).rand(9999,9999999).rand(9999,9999999);
		
		$js = '
		<script>
		function fc_add_friend_'.$random.'() {
			FB.ui({ 
				method: \'friends\',
				id: \''.$id.'\',
			});
		};
		
		</script>
		';
		
		$content = '<a href="javascript:" onclick="fc_add_friend_'.$random.'()">'.$title.'</a>';
		
		return $content.$js;
	}




}
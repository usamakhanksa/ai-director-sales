<?php echo script_tag("application/views/$theme/assets/js/jquery.cookie.js"); ?>
<?php

?>
	<section class="cookie-bar ">
  			<?php echo $cookie_text; ?>
				<a href="#" id="cookie-understand" class="btn btn-sm" style="background-color: #fff;color: #1f3c88;"><?php echo mlx_get_lang('I Agree');?></a>
	</section>
	
	<style>
	
.cookie-bar {
  position: fixed;
  width: 100%;
  bottom : 0;
  right: 0;
  left: 0;
  height: auto;
  text-align: center;
  line-height: 22px;
  background: #666666;
  color: white;
  font-size: 14px;
  font-family: "Lato", sans-serif;
  font-weight: 100;
  transition: 0.8s;
  animation: slideIn 0.8s;
  animation-delay: 0.8s;
  z-index:9999;
  display:none;
}
.cookie-bar .message {
  white-space: nowrap;
  text-shadow: 0 1px 0 #cc0000;
}
.cookie-bar p{
	margin:0px;
	padding: 10px 0px;
}
@media (max-width: 767px) {
  .cookie-bar .message {
    display: none;
  }
}
.cookie-bar .mobile {
  display: none;
}
@media (max-width: 767px) {
  .cookie-bar .mobile {
    display: inline-block;
  }
}

@keyframes slideIn {
  0% {
    transform: translateY(-50px);
  }
  100% {
    transform: translateY(0);
  }
}
.close-cb {
  border: none;
  color: white;
  background: #990000;
  position: absolute;
  display: inline-block;
  right: 10px;
  top: 0;
  cursor: pointer;
  border-radius: 3px;
  box-shadow: inset 0 0 3px 0 rgba(0, 0, 0, 0.2);
  line-height: 30px;
  height: 30px;
  width: 30px;
  font-size: 16px;
  font-weight: bold;
}
.close-cb:hover {
  background: #cc0000;
}

.checkbox-cb {
  display: none;
}
.checkbox-cb:checked + .cookie-bar {
  transform: translateY(-50px);
}
.cookie-bar--active{
	display:block;
}
.cookie-bar--inactive{
	display:none;
}
.cookie-bar a{
	color:#fff;
	font-weight:bold;
}
	</style>
	<script>

// Button ID
var buttonId = '#cookie-understand';
// Cookie name
var cookieName = 'realestate-web-accept-cookie';
// Cookie value
var cookieValue = 'accepted';
// Cookie expire (days)
var cookieExpire = 10;

// When click button, create cookie.
$(document).ready(function(){
	
  $(buttonId).click(function() {
	  
    if ($.cookie(cookieName) == null){
	
	  $.cookie(cookieName, cookieValue, { expires: cookieExpire, path: '/' });

      $('.cookie-bar').addClass('cookie-bar--inactive').fadeOut();
    }
	return false;
  });

  checkCookie();

  $('body').on('click', '#delete-cookie', function() {
    
    	$.removeCookie(cookieName, { path: '/' }); 
		checkCookie();    
		return false;
  });

}); 

// If no cookie, show the cookie bar.
function checkCookie(){
	
    if ($.cookie(cookieName) == null) {
      // No cookie = Show cookie bar
	  console.log($.cookie(cookieName));
      $('.cookie-bar').addClass('cookie-bar--active');
    }else{
		$('.cookie-bar').addClass('cookie-bar--inactive');
	}
}
	</script>
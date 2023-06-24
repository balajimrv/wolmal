<?php

/**
 * SocialEngineSolutions
 *
 * @category   Application_Sesbasic
 * @package    Sesbasic
 * @copyright  Copyright 2015-2016 SocialEngineSolutions
 * @license    http://www.socialenginesolutions.com/license/
 * @version    $Id: index.tpl 2015-10-28 00:00:00 SocialEngineSolutions $
 * @author     SocialEngineSolutions
 */
?>
<a href="javascript:;" id="SESscroolToToElement" onclick="SESscrollTopAnimated(1000)" class="scrollup"></a>
<script>
	window.addEventListener("scroll", function(event) {
    var top = this.scrollY;
		if (top > 100) {
			$('SESscroolToToElement').fade('in');
		} else {
			$('SESscroolToToElement').fade('out');
    }
	}, false);
	var stepTime = 20;
	var docBody = document.body;
	var focElem = document.documentElement;
	
	var scrollAnimationStep = function (initPos, stepAmount) {
			var newPos = initPos - stepAmount > 0 ? initPos - stepAmount : 0;
	
			docBody.scrollTop = focElem.scrollTop = newPos;
	
			newPos && setTimeout(function () {
					scrollAnimationStep(newPos, stepAmount);
			}, stepTime);
	}
	var SESscrollTopAnimated = function (speed) {
			var topOffset = docBody.scrollTop || focElem.scrollTop;
			var stepAmount = topOffset;
	
			speed && (stepAmount = (topOffset * stepTime)/speed);
	
			scrollAnimationStep(topOffset, stepAmount);
	};
</script>
<style>
/*Scroll Top Button*/
.scrollup {
	background-color:#2B2D2E;
	border:2px solid #ccc;
	background-image:url(application/modules/Sesbasic/externals/images/arrow-top.png);
	background-position:center center;
	background-repeat:no-repeat;
	border-radius:50%;
	width:40px;
	height:40px;
	position:fixed;
	bottom:20px;
	right:20px;
	display:block;
	z-index:11;
	opacity:0;
	visibility:hidden;
}
</style>
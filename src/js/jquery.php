<!-- jQuery -->
<script type="text/javascript" src="<?php echo ReturnUrl($_SERVER['REQUEST_URI']);?>/themes/js/jquery.min.js"></script>

<!-- Nice Scroll -->
<script type="text/javascript" src="<?php echo ReturnUrl($_SERVER['REQUEST_URI']);?>/themes/js/plugins/nicescroll/jquery.nicescroll.min.js"></script>
<!-- jQuery UI -->
<script type="text/javascript" src="<?php echo ReturnUrl($_SERVER['REQUEST_URI']);?>/themes/js/plugins/jquery-ui/jquery.ui.core.min.js"></script>
<script type="text/javascript" src="<?php echo ReturnUrl($_SERVER['REQUEST_URI']);?>/themes/js/plugins/jquery-ui/jquery.ui.widget.min.js"></script>
<script type="text/javascript" src="<?php echo ReturnUrl($_SERVER['REQUEST_URI']);?>/themes/js/plugins/jquery-ui/jquery.ui.mouse.min.js"></script>
<script type="text/javascript" src="<?php echo ReturnUrl($_SERVER['REQUEST_URI']);?>/themes/js/plugins/jquery-ui/jquery.ui.draggable.min.js"></script>
<script type="text/javascript" src="<?php echo ReturnUrl($_SERVER['REQUEST_URI']);?>/themes/js/plugins/jquery-ui/jquery.ui.resizable.min.js"></script>
<script type="text/javascript" src="<?php echo ReturnUrl($_SERVER['REQUEST_URI']);?>/themes/js/plugins/jquery-ui/jquery.ui.sortable.min.js"></script>
<!-- slimScroll -->
<script type="text/javascript" src="<?php echo ReturnUrl($_SERVER['REQUEST_URI']);?>/themes/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<!-- Bootstrap -->
<script type="text/javascript" src="<?php echo ReturnUrl($_SERVER['REQUEST_URI']);?>/themes/js/bootstrap.min.js"></script>
<!-- imagesLoaded -->
<script type="text/javascript" src="<?php echo ReturnUrl($_SERVER['REQUEST_URI']);?>/themes/js/plugins/imagesLoaded/jquery.imagesloaded.min.js"></script>
<!-- select2 -->
<script type="text/javascript" src="<?php echo ReturnUrl($_SERVER['REQUEST_URI']);?>/themes/js/plugins/select2/select2.min.js"></script>
<!-- Theme scripts -->
<script type="text/javascript" src="<?php echo ReturnUrl($_SERVER['REQUEST_URI']);?>/themes/js/application.min.js"></script>
<script type="text/javascript" src="<?php echo ReturnUrl($_SERVER['REQUEST_URI']);?>/themes/js/md5.min.js"></script>
<script type="text/javascript" src="<?php echo ReturnUrl($_SERVER['REQUEST_URI']);?>/themes/js/counter.js"></script>

<!-- TranslateClient BEGIN -->
<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement(
		{
		pageLanguage: 'th', 
		includedLanguages: 'th,en,my,vi,lo,km,tl,ms,id,zh-TW,ja', 
        layout:  google.translate.TranslateElement.InlineLayout.SIMPLE,
		autoDisplay: false,
		}, 
	'google_translate_element'
	);
}
</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
<!-- TranslateClient END -->

<script>
        $(document).ready(function() {
               
              $.ajaxSetup({
                  cache: false
              });
				  	  $('body').addClass('theme-grey');
        });
</script>
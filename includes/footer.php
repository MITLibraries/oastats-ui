				</section>
			</div>
			<div id="colophon">
				<div class="left block">
					<a href="http://libraries.mit.edu"><img src="/images/logo-black.png" alt="MIT Libraries"></a>
				</div>
				<div class="center block">
					Licensed under the <a href="http://creativecommons.org/licenses/by-nc/2.0/" target="_blank">Creative Commons Attribution Non-Commercial License</a> unless otherwise noted.
				</div>
				<div class="right block">
					<a href="http://libraries.mit.edu/scholarly/" id="homeFooter">Scholarly Communications</a> |
					<a href="http://libraries.mit.edu/faculty" id="homeFooter">Faculty</a>
				</div>
			</div>
		</div>
		<?php require_once('includes/include_mongo_disconnect.php'); ?>
		<script>
		$(document).ready(function() {

			var tabs = $('.tabs'),
			tab_a_selector = 'ul.ui-tabs-nav a';
			 
			tabs.tabs({ event: 'change' });
			 
			tabs.find( tab_a_selector ).click(function(){
				var state = {},
				id = $(this).closest( '.tabs' ).attr( 'id' ),
				idx = $(this).parent().prevAll().length;
				state[ id ] = idx;
				$.bbq.pushState( state );
			});
			 
			$(window).bind( 'hashchange', function(e) {
				tabs.each(function(){
					var idx = $.bbq.getState( this.id, true ) || 0;
					$(this).find( tab_a_selector ).eq( idx ).triggerHandler( 'change' );
				});
			})
			 
			$(window).trigger( 'hashchange' );

			$("#oafilter input").click(function() {
				// Which option was clicked on?
				var item = $(this).val();
				if(item=="all") {
					// Clicked on "all"
					$("#oafilter input[value!='all']").each(function() {
						console.log($(this).val());
						$(this).attr('checked', false);
					});
				} else {
					// Clicked on something else - make sure "all" isn't checked
					$("#oafilter #all").attr('checked', false);
					console.log('b');
				}
			});

			$("#exports li a").click(function() {
				alert('click');
			});
		});	
		</script>
	</body>
</html>			
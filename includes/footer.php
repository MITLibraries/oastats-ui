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
					<a href="http://libraries.mit.edu/scholarly/mit-open-access/open-access-at-mit/mit-open-access-policy/" id="homeFooter">MIT Faculty Open Access Policy</a>
					|
					<a href="http://dspace.mit.edu/handle/1721.1/49433">Open Access Articles</a>
				</div>
			</div>
			<div style="background-color: #fff;padding:1rem;">
				<h2>Debugging information goes here</h2>
<?php
	foreach($_SESSION as $key => $val) {
		echo "<p>".$key." = ".$val."</p>";
	}
?>				
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

			var exports = $("#exports");
			exports.on("click","li a",function() {
				var tool = $(this).attr("data-format");
				var tab = $(".ui-tabs-active").text();
				switch(tool) {
					case "csv":
						var uri = 'exports/csv.php';
						if(location.search){
							uri += location.search+'&tab='+tab;
						} else {
							uri += '?tab='+tab;
						}
						window.open(uri);
						break;
					case "png":
						alert('png coming soon...');
						break;
					case "pdf":
						alert('pdf coming soon...');
						break;
					default:
						alert('what?');
				}
			});

		});	
		</script>
	</body>
</html>			
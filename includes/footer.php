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
	</body>
	<script>
	$(document).ready(function() {

		$( "#tabs" ).tabs({
			beforeLoad: function( event, ui ) {
				ui.panel.html("Loading...");
				ui.jqXHR.error(function() {
					ui.panel.html(
					"Sorry, the contents of this tab could not be loaded right now." );
				});
			}
		});

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

	});	
	</script>
</html>			
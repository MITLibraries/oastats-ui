				</section>
			</div>
			<div id="colophon">
				<div class="left block">
					<a href="http://libraries.mit.edu"><img src="/images/logo-black.png" alt="MIT Libraries"></a>
				</div>
				<div class="center block">
					<p>Please email <a href="mailto:oastats@mit.edu">oastats@mit.edu</a> with any comments or questions regarding this site.</p>
					<p><a href="http://libraries.mit.edu/scholarly/mit-open-access/open-access-at-mit/mit-open-access-policy/" id="homeFooter">MIT Faculty Open Access Policy</a>
					|
					<a href="http://dspace.mit.edu/handle/1721.1/49433">Open Access Articles</a></p>
				</div>
				<div class="right block">
					<a href="http://web.mit.edu"><img src="/images/logo-mit-74x40.png" alt="MIT logo"></a>
				</div>
			</div>
<?php
// If the user is an admin, show the impersonate control
if(isset($_SESSION["admin"]) && $_SERVER["SCRIPT_NAME"] == "/author.php") {
	if($_SESSION["admin"] == true) {
		?>
			<div id="administration">
				<h2>Administration</h2>
				<p>You are currently logged in as: <strong><?php echo $_SESSION["fullname"];?></strong>.</p>
				<form>
					<label for="impersonate">
					Specify the MIT Kerberos username of the user whom you want to impersonate - leave blank to resume your real identity.
					<input type="text" name="impersonate" id="impersonate" value="<?php echo $reqA; ?>">
					</label>
					<input type="submit" value="Switch User">
				</form>
			</div>
		<?php		
	}
}

?>
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
							uri += location.search+'&tab='+tab+'&page='+location.pathname;
						} else {
							uri += '?tab='+tab+'&page='+location.pathname;
						}
						window.open(uri);
						break;
					case "png":
						alert('Sorry, export to PNG format is not available yet.');
						break;
					case "pdf":
						alert('Sorry, export to PDF format is not available yet.');
						break;
					default:
						alert('what?');
				}
			});

		});	
		</script>
	</body>
</html>			
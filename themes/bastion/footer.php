<!-- FOOTER –––––––––––––––––––––––––––––––––––––––
 The columns come from pages/footer/footer1.html,
 footer2.html and so on. Add or delete files there
 to change the number of columns; the framework
 counts them automatically.

 The bottom strip is fixed markup — put the legal
 line, the rating board notice and the publisher
 credit there.
–––––––––––––––––––––––––––––––––––––––––––––––– -->
<footer class="footercontainer">
	<div class="footercontent">
		<?php include 'required/footercolumns.php'; ?>
	</div>

	<div class="footerbottom">
		<p class="flush">&copy; <?php echo date("Y") . " " . $WebsiteTitle; ?>. All rights reserved.</p>
		<p class="flush">
			<a href="license">Legal</a> &middot;
			<a href="?rss">RSS</a> &middot;
			<a href="#maincontent">Back to top</a>
		</p>
	</div>
</footer>

<?php echo $pluginCalledBelowContent; ?>
<?php include 'navigation-options.php' ?>
</body>
</html>

<?php if (!isset($showBanner)) { $showBanner = false; } ?>
<?php
	/* Same as page-html, but the body runs the full width of
	   the container instead of being capped at the reading
	   measure. Use it for unit databases, comparison tables,
	   screenshot galleries and faction line-ups — anything
	   built out of .row / .column grids rather than prose.

	       <!-- pagelayout:page-wide -->

	   The .widelayout class is what removes the measure; the
	   page title keeps it, so headings stay readable. */
	$showInlineTitle = (!$showBanner) && ($pagename != "home" || $showhomepagetitle == true);
?>

	<!-- Primary Page Layout — full-width content
	–––––––––––––––––––––––––––––––––––––––––––––––––– -->
	<main class="contentcontainer widelayout">
		<?php if ($showInlineTitle) { ?>
		<div class="pagetitle">
			<?php if (isset($pagetitle) && $pagetitle != "") { ?>
				<h1><?php echo $pagetitle; ?></h1>
			<?php } else { ?>
				<h1><?php echo ucwords($pagename); ?></h1>
			<?php } ?>
		</div>
		<?php } ?>
		<div class="content">
			<div class="section group">
					<?php
						$filename = file_get_contents("./pages/" . $pagename . ".html");
						$parsed_content = parse_shortcodes($filename);
						echo $parsed_content;
					?>
			</div>
		</div>
	</main>

<!-- End Document
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->

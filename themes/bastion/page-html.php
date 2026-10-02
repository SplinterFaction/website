<?php if (!isset($showBanner)) { $showBanner = false; } ?>
<?php
	/* header.php has already printed the page title inside the
	   banner, so the inline <h1> would be a duplicate. Print it
	   only when there is no banner (i.e. a chromeless layout, or
	   if you delete the banner from header.php).

	   The date/author line still belongs here either way, under
	   the banner rather than on top of the art. */
	$showInlineTitle = (!$showBanner) && ($pagename != "home" || $showhomepagetitle == true);
	$showByline      = ($pagetype == "article");
?>

	<!-- Primary Page Layout — HTML, with title
	–––––––––––––––––––––––––––––––––––––––––––––––––– -->
	<main class="contentcontainer">
		<?php if ($showInlineTitle || $showByline) { ?>
		<div class="pagetitle">
			<?php if ($showInlineTitle) { ?>
				<?php if (isset($pagetitle) && $pagetitle != "") { ?>
					<h1><?php echo $pagetitle; ?></h1>
				<?php } else { ?>
					<h1><?php echo ucwords($pagename); ?></h1>
				<?php } ?>
			<?php } ?>
			<?php if ($showByline) { ?>
				<?php echo "<p class=\"pagedate\">" . formatDate($pagedate, 'pretty') . "</p>"; ?>
				<?php echo "<p class=\"pageauthor\">Filed by <i>" . $pageauthor . "</i></p>"; ?>
				<?php
					/* A post's own categories, from its pagecategory tag.
					   By convention each category has a page of the same name
					   at the pages root, so the chip links straight to it.
					   Untagged posts print nothing — the framework's
					   "uncategorized" fallback is a filtering concept, not
					   something worth badging on the article itself. */
					if (!empty($pagecategory)) {
						$postCats = array();
						foreach (explode(',', $pagecategory) as $cat) {
							$cat = strtolower(trim($cat));
							if ($cat !== '') { $postCats[] = $cat; }
						}
						if (!empty($postCats)) {
							echo '<p class="pagecategories">';
							foreach ($postCats as $cat) {
								echo '<a class="tag" href="' . htmlspecialchars($cat, ENT_QUOTES) . '">'
									. htmlspecialchars(ucwords(str_replace('-', ' ', $cat))) . '</a>';
							}
							echo '</p>';
						}
					}
				?>
			<?php } ?>
		</div>
		<?php } ?>
		<div class="content">
			<div class="section group">
					<?php
						$filename = file_get_contents("./pages/" . $pagename . ".html");
						// Parse and replace shortcodes
						$parsed_content = parse_shortcodes($filename);
						echo $parsed_content;
					?>
			</div>
		</div>
	</main>

<!-- End Document
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->

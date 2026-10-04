<?php if (!isset($showBanner)) { $showBanner = false; } ?>

<?php
	/* LANDING PAGE LAYOUT
	   ––––––––––––––––––––––––––––––––––––––––––––––––
	   Turn it on with a metadata line in the page file:

	       <!  -- pagelayout:page-landing -->
	       (written without the space; a real HTML
	        comment here would close this note early)

	   The hero is driven entirely by that page's own
	   metadata, so the front page is edited like any
	   other page:

	       pagetitle    the headline
	       pageexcerpt  the sub-line under it
	       pageimage    the key art behind it
	                    (also the OpenGraph image)

	   Everything after the hero comes from the page
	   file itself, full-bleed and unwrapped, so it can
	   lay out its own <section class="band"> blocks.
	   header.php suppresses the interior page banner
	   for this layout.
	   ––––––––––––––––––––––––––––––––––––––––––––––– */
?>
	<main class="blanklayout">

		<section class="hero" style="background-image: url('<?php echo htmlspecialchars($pageimage, ENT_QUOTES); ?>');">
			<div class="wrap">
				<div class="heroinner">

					<span class="eyebrow">Free &amp; open-source real-time strategy &middot; Built on the Recoil engine</span>

					<h1 class="display"><?php if (isset($pagetitle) && $pagetitle != "") { echo $pagetitle; } else { echo ucwords($pagename); } ?></h1>

					<p class="herosub"><?php echo $pageexcerpt; ?></p>

					<!-- Point these two wherever they should go. -->
					<div class="cluster">
						<a class="btn" href="download">Download</a>
						<a class="btn btn-ghost" href="getting-started">Read the field manual</a>
					</div>

					<!-- Hard facts, above the fold. Edit freely. -->
					<div class="herostrip">
						<span><b>Status</b> &nbsp;Playable now &middot; Available on itch.io &middot; Steam planned</span>
						<span><b>Platforms</b> &nbsp;Windows &middot; Linux</span>
						<span><b>Modes</b> &nbsp;Skirmish &middot; Survival &middot; Multiplayer</span>
					</div>

				</div>
			</div>
		</section>

		<?php
			$filename = file_get_contents("./pages/" . $pagename . ".html");
			$parsed_content = parse_shortcodes($filename);
			echo $parsed_content;
		?>

	</main>

<!-- End Document
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->

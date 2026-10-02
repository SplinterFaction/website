<?php if (!isset($showBanner)) { $showBanner = false; } ?>
<?php
	/* ARCHIVE — CARD GRID
	   ––––––––––––––––––––––––––––––––––––––––––––––––
	   Every .html file in pages/posts/ that carries a
	   pagetitle, pagedate, pageimage and pageexcerpt
	   becomes a card. That four-field requirement is
	   the framework's, kept here so posts behave the
	   same under every theme. pagecategory is NOT part
	   of it — it is optional by design, and requiring
	   it would make every untagged post vanish.

	   Add a postcategory tag to this page to filter
	   the listing to one category:

	       pagelayout:postarchives-styled
	       postcategory:engineering

	   Three things this theme adds on top:
	     - a missing image file falls back to the theme
	       placeholder instead of printing an error into
	       the page
	     - the first category becomes a clickable chip
	       on the card artwork; posts with no category
	       tag get no chip rather than an UNCATEGORIZED
	       badge
	     - a filtered page states the filter and links
	       its own ?rss=<category> feed

	   Use it with:
	       pagelayout:postarchives-styled
	   ––––––––––––––––––––––––––––––––––––––––––––––– */
	$showInlineTitle = (!$showBanner);
?>

	<!-- Post Archives — card grid
	–––––––––––––––––––––––––––––––––––––––––––––––––– -->
	<main class="contentcontainer widelayout">
		<?php if ($showInlineTitle && $pagename != "home") { ?>
		<div class="pagetitle">
			<?php if (isset($pagetitle) && $pagetitle != "") { ?>
				<h1><?php echo $pagetitle; ?></h1>
			<?php } else { ?>
				<h1><?php echo ucwords($pagename); ?></h1>
			<?php } ?>
		</div>
		<?php } ?>

		<div class="content">

			<?php
				/* When this page filters on a category, say so, and offer the
				   matching feed. The framework exposes per-category RSS as
				   ?rss=<category>, and RSS is handled before top-cache.php runs,
				   so a query string is safe on that URL specifically. */
				if (!empty($postcategory)) {
					$catSlug   = strtolower(trim($postcategory));
					$catPretty = ucwords(str_replace('-', ' ', $catSlug));
					echo '<div class="catbar">';
					echo '<span>Filtered to <b>' . htmlspecialchars($catPretty) . '</b></span>';
					echo '<span><a href="archives">All dispatches</a> &middot; '
					   . '<a href="?rss=' . htmlspecialchars($catSlug, ENT_QUOTES) . '">Subscribe to this category</a></span>';
					echo '</div>';
				}
			?>

			<?php
				// Folder containing the Article files
				$postsFolder = 'posts';

				// Shown when a post's pageimage points at a file
				// that isn't there. Swap for your own artwork.
				$fallbackImage = '/images/ph-thumb.svg';

				// Array to store file details
				$fileDetails = array();

				// Loop through each file in the Article folder
				foreach (glob("pages/$postsFolder/*.html") as $file) {
					// Read the file contents
					$contents = file_get_contents($file);

					// Extract pagetitle, date, thumbnail, and excerpt from HTML comments
					preg_match('/<!--\s+pagetitle:(.*?)\s+-->/s', $contents, $titleMatch);    //This will be the linktext
					preg_match('/<!--\s+pagedate:(.*?)\s+-->/s', $contents, $dateMatch);      //This needs to be mm/dd/yyyy format
					preg_match('/<!--\s+pageimage:(.*?)\s+-->/s', $contents, $imageMatch);    //Image filename with extension
					preg_match('/<!--\s+pageexcerpt:(.*?)\s+-->/s', $contents, $excerptMatch);//No real formatting here, just a blurb
					preg_match('/<!--\s+pageauthor:(.*?)\s+-->/s', $contents, $authorMatch);
					preg_match('/<!--\s+pagecategory:(.*?)\s+-->/s', $contents, $categoryMatch); //Optional. Comma-separated category list. Posts without it are treated as "uncategorized".

					// If all details are found, add them to the array
					if ($titleMatch && $dateMatch && $imageMatch && $excerptMatch) {
						$title    = trim($titleMatch[1]);
						$date     = trim($dateMatch[1]);
						$image    = trim($imageMatch[1]);
						$excerpt  = trim($excerptMatch[1]);
						$author   = $authorMatch ? trim($authorMatch[1]) : $WebsiteAuthor;

						// Categories are OPTIONAL. Legacy posts without a pagecategory tag must never be
						// dropped from archives, so this is deliberately NOT part of the required-fields
						// check above. Untagged posts are treated as belonging to "uncategorized".
						$categories = array();
						if ($categoryMatch) {
							foreach (explode(',', $categoryMatch[1]) as $cat) {
								$cat = strtolower(trim($cat));
								if ($cat !== '') { $categories[] = $cat; }
							}
						}
						// Remember whether the tag was actually present, separately from the
						// fallback value. The card chip is a real, clickable category, so it
						// is suppressed for posts that only landed in "uncategorized" by
						// default — a badge reading UNCATEGORIZED on every untagged post is
						// noise, not information.
						$tagged = !empty($categories);
						if (empty($categories)) { $categories = array('uncategorized'); }

						$fileDetails[] = array(
							'title'      => $title,
							'date'       => $date,
							'image'      => $image,
							'excerpt'    => $excerpt,
							'author'     => $author,
							'categories' => $categories,
							'tagged'     => $tagged,
							'filename'   => $file
						);
					}
				}

				/* CATEGORY FILTER */
				// $postcategory comes from this page's own <!-- postcategory: ... --> tag,
				// extracted by required/vitalfunctions.php. When the tag is absent (e.g. the
				// main archives page), no filtering happens and every post is listed.
				// A page with <!-- postcategory: uncategorized --> lists all untagged posts.
				if (!empty($postcategory)) {
					$filterCategory = strtolower(trim($postcategory));
					$fileDetails = array_values(array_filter($fileDetails, function ($post) use ($filterCategory) {
						return in_array($filterCategory, $post['categories']);
					}));
				}
				/* END CATEGORY FILTER */

				/* SORTING METHODS */

				// Sort the array by date in descending order
				usort($fileDetails, function ($a, $b) {
					return strtotime($b['date']) - strtotime($a['date']);
				});

				// Sort the array by title in ascending order (A-Z)
				// usort($fileDetails, function ($a, $b) {
					// return strcmp($a['title'], $b['title']);
				// });

				/* END SORTING METHODS */

				// Pagination. Nine fills three rows of three
				// cleanly on a wide screen.
				$itemsPerPage = 9;
				$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
				if ($page < 1) { $page = 1; }
				$startIndex = ($page - 1) * $itemsPerPage;
				$fileDetailsPage = array_slice($fileDetails, $startIndex, $itemsPerPage);

				// Output the cards for the current page.
				// Grid comes from css/flexgridsystem.css:
				// .row + .column + .flex-basis-*; row-stretch
				// (this theme) makes the cards equal height.
				if (count($fileDetailsPage) == 0) {
					if (!empty($postcategory)) {
						echo '<p class="muted">No dispatches filed in this category yet.</p>';
					} else {
						echo '<p class="muted">No dispatches filed yet.</p>';
					}
				}

				echo '<div class="row row-stretch">';
				foreach ($fileDetailsPage as $fileDetail) {
					$link = $postsFolder . '/' . basename($fileDetail['filename'], '.html');
					$dateFormatted = formatDate($fileDetail['date'], 'M j, Y');
					$dateISO = formatDate($fileDetail['date'], 'Y-m-d');
					$image = file_exists($fileDetail['image']) ? $fileDetail['image'] : $fallbackImage;

					// Category labels. By convention each category has a page of the same name at the
					// pages root (e.g. pages/tutorials.html), so the label links to /tutorials.
					$categoryLinks = array();
					foreach ($fileDetail['categories'] as $cat) {
						$categoryLinks[] = '<a href="' . htmlspecialchars($cat, ENT_QUOTES) . '">'
							. htmlspecialchars(ucwords(str_replace('-', ' ', $cat))) . '</a>';
					}

					echo '<div class="column flex-basis-300">';
					echo '<article class="dispatch lift">';

					echo '<div class="dispatchmedia">';
					if ($fileDetail['tagged']) {
						$primary = $fileDetail['categories'][0];
						echo '<a class="tag dispatchtag" href="' . htmlspecialchars($primary, ENT_QUOTES) . '">'
							. htmlspecialchars(ucwords(str_replace('-', ' ', $primary))) . '</a>';
					}
					echo '<a href="' . $link . '" tabindex="-1" aria-hidden="true">';
					echo '<img src="' . htmlspecialchars($image, ENT_QUOTES) . '" alt="" loading="lazy">';
					echo '</a>';
					echo '</div>';

					echo '<div class="dispatchbody">';
					echo '<h2 class="dispatchtitle"><a href="' . $link . '">' . $fileDetail['title'] . '</a></h2>';
					echo '<p class="dispatchmeta"><time datetime="' . $dateISO . '">' . $dateFormatted . '</time> &middot; <i>' . $fileDetail['author'] . '</i></p>';
					// The chip already names the first category; only spell the
					// list out when there is more than one to spell out.
					if ($fileDetail['tagged'] && count($categoryLinks) > 1) {
						echo '<p class="dispatchcats">Filed in ' . implode(', ', $categoryLinks) . '</p>';
					}
					echo '<p class="dispatchexcerpt">' . $fileDetail['excerpt'] . '</p>';
					echo '<a class="dispatchmore" href="' . $link . '">Read dispatch</a>';
					echo '</div>';

					echo '</article>';
					echo '</div>';
				}
				echo '</div>';

				// Pagination links
				$totalPages = ceil(count($fileDetails) / $itemsPerPage);
				if ($totalPages > 1) {
					echo '<nav class="pagination" aria-label="Archive pages">Page: ';
					for ($i = 1; $i <= $totalPages; $i++) {
						if ($i == $page) {
							echo "<span aria-current='page'>$i</span> ";
						} else {
							echo "<a href='$pagename?page=$i'>$i</a> ";
						}
					}
					echo '</nav>';
				}
			?>

		</div>
	</main>

<!-- End Document
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->

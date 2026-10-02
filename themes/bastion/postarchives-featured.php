<?php if (!isset($showBanner)) { $showBanner = false; } ?>
<?php
	/* ARCHIVE — LEAD STORY + GRID
	   ––––––––––––––––––––––––––––––––––––––––––––––––
	   Identical scanning to postarchives-styled, but
	   the newest post is pulled out and given a wide
	   two-column card at the top. Good for a news page
	   where one announcement should dominate.

	   The lead only appears on page 1 — on later pages
	   every post is treated equally, which is what you
	   want when someone is browsing backwards.

	   Supports the same optional postcategory tag as
	   the other archive layouts; on a filtered page the
	   lead is simply the newest post in that category.

	       pagelayout:postarchives-featured
	       postcategory:engineering
	   ––––––––––––––––––––––––––––––––––––––––––––––– */
	$showInlineTitle = (!$showBanner);
?>

	<!-- Post Archives — featured lead + grid
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
				/* Announce an active category filter and link its feed. */
				if (!empty($postcategory)) {
					$catSlug   = strtolower(trim($postcategory));
					$catPretty = ucwords(str_replace('-', ' ', $catSlug));
					echo '<div class="catbar">';
					echo '<span>Filtered to <b>' . htmlspecialchars($catPretty) . '</b></span>';
					echo '<span><a href="archives">All dispatches</a> &middot; '
					   . '<a href="?rss=' . htmlspecialchars($catSlug, ENT_QUOTES) . '">Subscribe to this category</a></span>';
					echo '</div>';
				}

				$postsFolder = 'posts';
				$fallbackImage = '/images/ph-thumb.svg';
				$fileDetails = array();

				foreach (glob("pages/$postsFolder/*.html") as $file) {
					$contents = file_get_contents($file);

					preg_match('/<!--\s+pagetitle:(.*?)\s+-->/s', $contents, $titleMatch);
					preg_match('/<!--\s+pagedate:(.*?)\s+-->/s', $contents, $dateMatch);
					preg_match('/<!--\s+pageimage:(.*?)\s+-->/s', $contents, $imageMatch);
					preg_match('/<!--\s+pageexcerpt:(.*?)\s+-->/s', $contents, $excerptMatch);
					preg_match('/<!--\s+pageauthor:(.*?)\s+-->/s', $contents, $authorMatch);
					preg_match('/<!--\s+pagecategory:(.*?)\s+-->/s', $contents, $categoryMatch); //Optional. Comma-separated category list.

					if ($titleMatch && $dateMatch && $imageMatch && $excerptMatch) {
						// Categories are OPTIONAL — never add them to the check above.
						// Untagged posts are treated as belonging to "uncategorized".
						$categories = array();
						if ($categoryMatch) {
							foreach (explode(',', $categoryMatch[1]) as $cat) {
								$cat = strtolower(trim($cat));
								if ($cat !== '') { $categories[] = $cat; }
							}
						}
						$tagged = !empty($categories);
						if (empty($categories)) { $categories = array('uncategorized'); }

						$fileDetails[] = array(
							'title'      => trim($titleMatch[1]),
							'date'       => trim($dateMatch[1]),
							'image'      => trim($imageMatch[1]),
							'excerpt'    => trim($excerptMatch[1]),
							'author'     => $authorMatch ? trim($authorMatch[1]) : $WebsiteAuthor,
							'categories' => $categories,
							'tagged'     => $tagged,
							'filename'   => $file
						);
					}
				}

				/* CATEGORY FILTER */
				// $postcategory comes from this page's own postcategory tag,
				// extracted by required/vitalfunctions.php. Absent tag = no filter.
				if (!empty($postcategory)) {
					$filterCategory = strtolower(trim($postcategory));
					$fileDetails = array_values(array_filter($fileDetails, function ($post) use ($filterCategory) {
						return in_array($filterCategory, $post['categories']);
					}));
				}
				/* END CATEGORY FILTER */

				// Newest first
				usort($fileDetails, function ($a, $b) {
					return strtotime($b['date']) - strtotime($a['date']);
				});

				$itemsPerPage = 9;
				$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
				if ($page < 1) { $page = 1; }
				$startIndex = ($page - 1) * $itemsPerPage;
				$fileDetailsPage = array_slice($fileDetails, $startIndex, $itemsPerPage);

				// Pull the lead out of the run, page 1 only.
				$lead = null;
				if ($page == 1 && count($fileDetailsPage) > 0) {
					$lead = array_shift($fileDetailsPage);
				}

				/* Card renderer, shared by both shapes below.
				   $wide switches on the two-column lead form. */
				if (!function_exists('bastion_dispatch')) {
				function bastion_dispatch($item, $postsFolder, $fallbackImage, $wide = false) {
					$link  = $postsFolder . '/' . basename($item['filename'], '.html');
					$when  = formatDate($item['date'], 'M j, Y');
					$iso   = formatDate($item['date'], 'Y-m-d');
					$image = file_exists($item['image']) ? $item['image'] : $fallbackImage;

					$categoryLinks = array();
					foreach ($item['categories'] as $cat) {
						$categoryLinks[] = '<a href="' . htmlspecialchars($cat, ENT_QUOTES) . '">'
							. htmlspecialchars(ucwords(str_replace('-', ' ', $cat))) . '</a>';
					}

					$media  = '<div class="dispatchmedia">';
					if ($item['tagged']) {
						$primary = $item['categories'][0];
						$media .= '<a class="tag dispatchtag" href="' . htmlspecialchars($primary, ENT_QUOTES) . '">'
							. htmlspecialchars(ucwords(str_replace('-', ' ', $primary))) . '</a>';
					}
					$media .= '<a href="' . $link . '" tabindex="-1" aria-hidden="true">';
					$media .= '<img src="' . htmlspecialchars($image, ENT_QUOTES) . '" alt="" loading="lazy">';
					$media .= '</a></div>';

					$titleTag = $wide ? 'h2' : 'h2';
					$body  = '<div class="dispatchbody">';
					$body .= '<' . $titleTag . ' class="dispatchtitle"><a href="' . $link . '">' . $item['title'] . '</a></' . $titleTag . '>';
					$body .= '<p class="dispatchmeta"><time datetime="' . $iso . '">' . $when . '</time> &middot; <i>' . $item['author'] . '</i></p>';
					// The lead card has room, so it always spells the list out;
					// grid cards only do so when the chip does not cover it.
					if ($item['tagged'] && ($wide || count($categoryLinks) > 1)) {
						$body .= '<p class="dispatchcats">Filed in ' . implode(', ', $categoryLinks) . '</p>';
					}
					$body .= '<p class="dispatchexcerpt">' . $item['excerpt'] . '</p>';
					$body .= '<a class="dispatchmore" href="' . $link . '">Read dispatch</a>';
					$body .= '</div>';

					if ($wide) {
						return '<article class="dispatch-lead">'
							 . '<div class="row">'
							 . '<div class="column flex-basis-400">' . $media . '</div>'
							 . '<div class="column flex-basis-400">' . $body . '</div>'
							 . '</div></article>';
					}

					return '<article class="dispatch lift">' . $media . $body . '</article>';
				}
				}

				if ($lead === null && count($fileDetailsPage) == 0) {
					if (!empty($postcategory)) {
						echo '<p class="muted">No dispatches filed in this category yet.</p>';
					} else {
						echo '<p class="muted">No dispatches filed yet.</p>';
					}
				}

				if ($lead !== null) {
					echo bastion_dispatch($lead, $postsFolder, $fallbackImage, true);
				}

				if (count($fileDetailsPage) > 0) {
					echo '<div class="row row-stretch">';
					foreach ($fileDetailsPage as $fileDetail) {
						echo '<div class="column flex-basis-300">';
						echo bastion_dispatch($fileDetail, $postsFolder, $fallbackImage);
						echo '</div>';
					}
					echo '</div>';
				}

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

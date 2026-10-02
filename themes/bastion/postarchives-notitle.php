<?php
	/* ARCHIVE — PLAIN LIST, NO TITLE BLOCK
	   ––––––––––––––––––––––––––––––––––––––––––––––––
	   The quiet variant: one post per row, thumbnail
	   beside the text, hairline between entries. Stays
	   inside the reading measure, so it suits a small
	   dev-log more than a news hub.

	       pagelayout:postarchives-notitle

	   Add a postcategory tag to filter the listing to
	   one category; categories appear inline in each
	   entry's meta line.

	   The page banner in header.php still shows the
	   title; this only drops the in-page one.
	   ––––––––––––––––––––––––––––––––––––––––––––––– */
?>

	<!-- Post Archives — plain list, no title
	–––––––––––––––––––––––––––––––––––––––––––––––––– -->
	<main class="contentcontainer">
		<div class="content content-wide">
			<div class="section group">

				<?php
					$postsFolder = 'posts';

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
							// Categories are OPTIONAL. Never add them to the check above:
							// requiring one would drop every untagged post from every archive.
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

					/* SORTING METHODS */

					// Sort the array by date in descending order
					usort($fileDetails, function ($a, $b) {
						return strtotime($b['date']) - strtotime($a['date']);
					});

					/* END SORTING METHODS */

					$itemsPerPage = 8;
					$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
					if ($page < 1) { $page = 1; }
					$startIndex = ($page - 1) * $itemsPerPage;
					$fileDetailsPage = array_slice($fileDetails, $startIndex, $itemsPerPage);

					if (count($fileDetailsPage) == 0) {
						if (!empty($postcategory)) {
							echo '<p class="muted">No dispatches filed in this category yet.</p>';
						} else {
							echo '<p class="muted">No dispatches filed yet.</p>';
						}
					}

					foreach ($fileDetailsPage as $fileDetail) {
						$link  = $postsFolder . '/' . basename($fileDetail['filename'], '.html');
						$when  = formatDate($fileDetail['date'], 'M j, Y');
						$iso   = formatDate($fileDetail['date'], 'Y-m-d');
						$image = file_exists($fileDetail['image']) ? $fileDetail['image'] : $fallbackImage;

						echo '<article class="postarchive-item">';
						echo '<div class="row row-tight">';

						echo '<div class="column flex-basis-150">';
						echo '<a class="mediaframe ratio-16x9" href="' . $link . '" tabindex="-1" aria-hidden="true">';
						echo '<img src="' . htmlspecialchars($image, ENT_QUOTES) . '" alt="" loading="lazy">';
						echo '</a>';
						echo '</div>';

						echo '<div class="column flex-basis-400" style="text-align:left">';
						echo '<h2 class="dispatchtitle"><a href="' . $link . '">' . $fileDetail['title'] . '</a></h2>';
						$metaLine = '<time datetime="' . $iso . '">' . $when . '</time> &middot; <i>' . $fileDetail['author'] . '</i>';
						// The list view has no artwork chip, so categories go inline.
						if ($fileDetail['tagged']) {
							$categoryLinks = array();
							foreach ($fileDetail['categories'] as $cat) {
								$categoryLinks[] = '<a href="' . htmlspecialchars($cat, ENT_QUOTES) . '">'
									. htmlspecialchars(ucwords(str_replace('-', ' ', $cat))) . '</a>';
							}
							$metaLine .= ' &middot; ' . implode(', ', $categoryLinks);
						}
						echo '<p class="dispatchmeta">' . $metaLine . '</p>';
						echo '<p class="dispatchexcerpt">' . $fileDetail['excerpt'] . '</p>';
						echo '<a class="dispatchmore" href="' . $link . '">Read dispatch</a>';
						echo '</div>';

						echo '</div>';
						echo '</article>';
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
		</div>
	</main>

<!-- End Document
  –––––––––––––––––––––––––––––––––––––––––––––––––– -->

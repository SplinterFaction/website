<?php
/* LATEST DISPATCHES — reusable block
   ––––––––––––––––––––––––––––––––––––––––––––––––––––
   Drops the newest posts from pages/posts/ into any
   page as a row of cards. Use it from a page file with
   the framework's php shortcode:

       [php] $limit = 3; include 'themes/bastion/partial-latest-dispatches.php'; [/php]

   Set $limit before including to change how many.
   Omit it and you get three. Set $category to narrow
   it to one category:

       [php] $limit = 3; $category = 'engineering';
             include 'themes/bastion/partial-latest-dispatches.php'; [/php]

   Why a partial rather than code in the page: the
   landing page decides WHERE the block sits, the theme
   decides what it looks like. Swap themes and the news
   block re-skins with everything else.

   The shortcode runs this inside a function, so the
   config globals have to be pulled in explicitly.
   –––––––––––––––––––––––––––––––––––––––––––––––––– */

global $theme, $WebsiteAuthor;

$dispatchLimit    = isset($limit) ? (int) $limit : 3;
$dispatchCategory = isset($category) ? strtolower(trim($category)) : '';
$dispatchFolder   = 'posts';
$dispatchFallback = '/images/ph-thumb.svg';
$dispatchItems    = array();

foreach (glob("pages/$dispatchFolder/*.html") as $dispatchFile) {
	$dispatchRaw = file_get_contents($dispatchFile);

	preg_match('/<!--\s+pagetitle:(.*?)\s+-->/s', $dispatchRaw, $dTitle);
	preg_match('/<!--\s+pagedate:(.*?)\s+-->/s', $dispatchRaw, $dDate);
	preg_match('/<!--\s+pageimage:(.*?)\s+-->/s', $dispatchRaw, $dImage);
	preg_match('/<!--\s+pageexcerpt:(.*?)\s+-->/s', $dispatchRaw, $dExcerpt);
	preg_match('/<!--\s+pageauthor:(.*?)\s+-->/s', $dispatchRaw, $dAuthor);
	preg_match('/<!--\s+pagecategory:(.*?)\s+-->/s', $dispatchRaw, $dCategories);

	if ($dTitle && $dDate && $dImage && $dExcerpt) {
		// Optional, exactly as in the archive layouts.
		$dCatList = array();
		if ($dCategories) {
			foreach (explode(',', $dCategories[1]) as $cat) {
				$cat = strtolower(trim($cat));
				if ($cat !== '') { $dCatList[] = $cat; }
			}
		}
		$dTagged = !empty($dCatList);
		if (empty($dCatList)) { $dCatList = array('uncategorized'); }

		$dispatchItems[] = array(
			'title'      => trim($dTitle[1]),
			'date'       => trim($dDate[1]),
			'image'      => trim($dImage[1]),
			'excerpt'    => trim($dExcerpt[1]),
			'author'     => $dAuthor ? trim($dAuthor[1]) : $WebsiteAuthor,
			'categories' => $dCatList,
			'tagged'     => $dTagged,
			'filename'   => $dispatchFile
		);
	}
}

// Optional category narrowing, same matching rules as the archives.
if ($dispatchCategory !== '') {
	$dispatchItems = array_values(array_filter($dispatchItems, function ($post) use ($dispatchCategory) {
		return in_array($dispatchCategory, $post['categories']);
	}));
}

usort($dispatchItems, function ($a, $b) {
	return strtotime($b['date']) - strtotime($a['date']);
});

$dispatchItems = array_slice($dispatchItems, 0, $dispatchLimit);

if (count($dispatchItems) == 0) {
	echo '<p class="muted textalign-center">No dispatches filed yet.</p>';
} else {
	echo '<div class="row row-stretch">';
	foreach ($dispatchItems as $dispatchItem) {
		$dLink  = $dispatchFolder . '/' . basename($dispatchItem['filename'], '.html');
		$dWhen  = formatDate($dispatchItem['date'], 'M j, Y');
		$dISO   = formatDate($dispatchItem['date'], 'Y-m-d');
		$dSrc   = file_exists($dispatchItem['image']) ? $dispatchItem['image'] : $dispatchFallback;

		echo '<div class="column flex-basis-300">';
		echo '<article class="dispatch lift">';
		echo '<div class="dispatchmedia">';
		if ($dispatchItem['tagged']) {
			$dPrimary = $dispatchItem['categories'][0];
			echo '<a class="tag dispatchtag" href="' . htmlspecialchars($dPrimary, ENT_QUOTES) . '">'
				. htmlspecialchars(ucwords(str_replace('-', ' ', $dPrimary))) . '</a>';
		}
		echo '<a href="' . $dLink . '" tabindex="-1" aria-hidden="true">';
		echo '<img src="' . htmlspecialchars($dSrc, ENT_QUOTES) . '" alt="" loading="lazy">';
		echo '</a></div>';
		echo '<div class="dispatchbody">';
		echo '<h3 class="dispatchtitle"><a href="' . $dLink . '">' . $dispatchItem['title'] . '</a></h3>';
		echo '<p class="dispatchmeta"><time datetime="' . $dISO . '">' . $dWhen . '</time> &middot; <i>' . $dispatchItem['author'] . '</i></p>';
		echo '<p class="dispatchexcerpt">' . $dispatchItem['excerpt'] . '</p>';
		echo '<a class="dispatchmore" href="' . $dLink . '">Read dispatch</a>';
		echo '</div></article></div>';
	}
	echo '</div>';
}
?>

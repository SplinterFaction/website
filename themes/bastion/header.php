<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#">
<head>
	<base href="<?php echo $WebsiteURL . '/'; ?>">

	<!-- Basic Page Needs
	–––––––––––––––––––––––––––––––––––––––––––––––––– -->
	<meta charset="utf-8">
	<title><?php if ($pagename != "home") { if (isset($pagetitle)) { echo ucwords($pagetitle) . " - "; } else { echo ucwords($pagename) . " - "; }} echo $WebsiteTitle; ?></title>

	<?php /* Set Language Country */ ?>
	<html lang="<?php echo $WebsiteLanguage; ?>">
	<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#" lang="<?php echo $WebsiteLanguageCountry; ?>">

	<meta name="referrer" content="strict-origin">
	<link rel="canonical" href="<?php echo $currentURL; ?>">

	<?php /* Set a fallback page excerpt/description */ ?>
	<?php if ($pageexcerpt == "") { $pageexcerpt = $WebsiteDescription; }?>
	<meta name="description" content="<?php echo $pageexcerpt; ?>">

	<?php /* Set a fallback page author */ ?>
	<?php if ($pageauthor == "") { $pageauthor = $WebsiteAuthor; }?>
	<meta name="author" content="<?php echo $pageauthor; ?>">

	<?php /* Set a fallback page keywords */ ?>
	<?php if ($pagekeywords == "") { $pagekeywords = $WebsiteKeywords; }?>
	<meta name="keywords" content="<?php echo $pagekeywords; ?>">

	<meta name="robots" content="robots.txt">

	<?php /* Opengraph Garbage goes here */ ?>
	<meta property="og:title" content="<?php if (isset($pagetitle)) { echo ucwords($pagetitle); } else { echo ucwords($pagename); } ?> - <?php echo $WebsiteTitle; ?>">
	<meta property="og:description" content="<?php echo $pageexcerpt; ?>">
	<meta property="og:url" content="<?php echo $currentURL; ?>">

	<?php /* Set a default image */ ?>
	<?php if ($pageimage == "") { $pageimage = $WebsiteImage; } ?>
	<meta property="og:image" content="<?php echo $WebsiteURL . "/" . $pageimage; ?>">

	<?php /* Set a default pagetype (E.G. website, article, blog, profile, video, music, book, product) */ ?>
	<?php if ($pagetype == "") { $pagetype = "website"; }?>
	<meta property="og:type" content="<?php echo $pagetype ?>">

	<meta property="og:site_name" content="<?php echo $WebsiteTitle; ?>">
	<meta property="og:locale" content="<?php echo $WebsiteLanguageLocale; ?>">

	<?php /* Convert date to YYYY-MM-DD*/ ?>
	<?php
		if ($pagedate != "") {
			$outputDateFormat1 = formatDate($pagedate, 'Y-m-d H:i:s');
			$pagedate = $outputDateFormat1;
		} else {
			// Get the last modified time of the file
			$pagefilename = "pages/" . $pagename .".html";
			$lastModifiedTime = filemtime($pagefilename);
			$lastModifiedDate = date("Y-m-d H:i:s", $lastModifiedTime);
			$pagedate = $lastModifiedDate;
		}
	?>
	<meta property="og:article:published_time" content="<?php echo $pagedate; ?>">

	<?php /* A dark theme should say so, so form controls and
	         scrollbars are drawn dark by the browser too. */ ?>
	<meta name="color-scheme" content="dark">
	<meta name="theme-color" content="#0b0e12">

	<!-- Mobile Specific Metas
	–––––––––––––––––––––––––––––––––––––––––––––––––– -->
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- FONT
	–––––––––––––––––––––––––––––––––––––––––––––––––– -->
	<?php /* Bastion uses system fonts only. Nothing is downloaded,
	         nothing blocks the first paint, and there is no build
	         step. If you want a display face for headings, add the
	         @font-face here and point --font-heading at it in
	         custom.css — no other rule needs to change. */ ?>

	<!-- CSS
	–––––––––––––––––––––––––––––––––––––––––––––––––– -->
	<link rel="stylesheet" href="css/normalize.css">
	<link rel="stylesheet" href="css/base.css">
	<link rel="stylesheet" href="css/flexgridsystem.css">
	<link rel="stylesheet" href="themes/<?php echo $theme; ?>/navigation.css">
	<link rel="stylesheet" href="themes/<?php echo $theme; ?>/custom.css">

	<!-- Favicon
	–––––––––––––––––––––––––––––––––––––––––––––––––– -->
	<!-- <link rel="icon" type="image/png" href="images/favicon.png"> -->
	<link rel="icon" href="images/favicon.svg" type="image/svg+xml">

</head>
<?php include 'required/metainfo.php'; ?>
<?php if ($loadplugins == true) { include 'plugins/plugins.php'; } ?>

	<!-- Beginning of actual page layout
	––––––––––––––––––––––––––––––––––––––––––––––––––
	 Everything above this line is metadata and SEO
	 and is unchanged from the skeleton theme.
	 Everything below is yours to rearrange.
	–––––––––––––––––––––––––––––––––––––––––––––––– -->
<body>

	<a class="skiplink" href="#maincontent">Skip to content</a>

	<?php
		/* Work out the layout before anything is drawn, because
		   two decisions depend on it:
		     - page-landing draws its own full-height hero, so the
		       interior page banner must be suppressed
		     - page-blank is deliberately chrome-free
		   Set here rather than at the include below so the header
		   and the layout agree. */
		if (!isset($pagelayout) || $pagelayout == "") { $pagelayout = "page-md"; }
		$chromelessLayouts = array("page-landing", "page-blank");
		$showBanner = !in_array($pagelayout, $chromelessLayouts);
	?>

	<!-- BRAND BAR ––––––––––––––––––––––––––––––––––
	 Logo, wordmark and the one call to action you
	 most want clicked. Scrolls away; the nav below
	 it is what sticks.

	 Swap the placeholder emblem for your own mark
	 and point the button wherever it should go
	 (a store page, a signup form, a trailer).
	–––––––––––––––––––––––––––––––––––––––––––––– -->
	<div class="brandbar">
		<div class="wrap brandbar-inner">
			<a class="brand" href="">
				<img src="/images/ph-emblem.svg" alt="" width="40" height="40">
				<span>
					<span class="brandname"><?php echo $WebsiteTitle; ?></span>
					<span class="brandtag">Real-time strategy</span>
				</span>
			</a>
			<div class="cluster">
				<a class="btn btn-ghost btn-small" href="getting-started">Get started</a>
				<a class="btn btn-small" href="download">Download</a>
			</div>
		</div>
	</div>

	<?php include 'navigation.php'; ?>

	<?php if ($showBanner) { ?>
	<!-- PAGE BANNER ––––––––––––––––––––––––––––––––
	 Interior pages get a short banner using their
	 own pageimage as the background. The layouts
	 below suppress their own <h1> when this is
	 showing, so the title appears exactly once.
	–––––––––––––––––––––––––––––––––––––––––––––– -->
	<header class="pagebanner" style="background-image: url('<?php echo htmlspecialchars($pageimage, ENT_QUOTES); ?>');">
		<div class="wrap">
			<?php
			/* Build the trail from the page's own path, so
			   pages/posts/supply-lines.html reads
			   Home / Posts / Supply Lines rather than one
			   run-together string. Only the last segment is
			   linked to nothing, since intermediate folders
			   are not pages in this framework. */
			$crumbs = explode('/', $pagename);
			echo '<p class="breadcrumb"><a href="">Home</a>';
			foreach ($crumbs as $crumb) {
				$crumb = ucwords(str_replace(array('-', '_'), ' ', $crumb));
				echo ' <span aria-hidden="true">/</span> ' . htmlspecialchars($crumb);
			}
			echo '</p>';
		?>
		<span class="bannertitle"><?php if (isset($pagetitle) && $pagetitle != "") { echo $pagetitle; } else { echo ucwords($pagename); } ?></span>
		</div>
	</header>
	<?php } ?>

<a id="maincontent"></a>

<?php include $pagelayout . ".php" ?>
<?php include 'footer.php'; ?>

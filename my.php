<?php
// $Id$
$_SERVER['BASE_PAGE'] = 'my.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/prepend.inc';

// Try to make this page non-cached
header_nocache();

// Languages array copy and options to list
$langs = $LANGUAGES; $options = array();

// We have post data, and it is an available language
if (isset($_POST['my_lang']) && isset($langs[$_POST['my_lang']])) {
    
    // Set the language preference
    myphpnet_language($_POST['my_lang']);
    
    // Add this as first option, selected
    $options[] = '<option value="' . $_POST['my_lang'] . '" selected>' .
                 $langs[$_POST['my_lang']] . "</option>\n";
    
    // Remove, so it is not listed two times
    unset($langs[$_POST['my_lang']]);
}

// We have recevied a cookie and it is an available language
elseif (isset($langs[myphpnet_language()])) {

    // Add this as first option, selected
    $options[] = '<option value="' . myphpnet_language() . '" selected>' .
                 $langs[myphpnet_language()] . "</option>\n";
    
    // Remove, so it is not listed two times
    unset($langs[myphpnet_language()]);
}

// We have no cookie and no form submitted
else {
    // Add this as first option, selected
    $options[] = "<option value=\"not_set\" selected=\"selected\">Not Set</option>\n";
}

// Add all other languages
foreach ($langs as $code => $name) {
    $options[] = '<option value="' . $code . '">' . $name . "</option>\n";
}

// Assemble form from collected data
$langpref = "<select name=\"my_lang\">\n" .
            join("", $options) . "</select>\n";
            
// Check URL shortcut fallback setting
if (isset($_POST['urlsearch'])) {
    myphpnet_urlsearch($_POST['urlsearch']);
}

myphpnet_save();

commonHeader("My PHP.net");
?>

<form action="/my.php" method="post">
<h1>My PHP.net</h1>

<p>
This page allows you to customize the PHP.net site to some degree
to your own liking. Currently you can only set the language you
prefer, thus overriding any other automatic language selection.
</p>

<p>
These settings will be active on all official PHP.net mirror sites,
and are stored using cookies, so you need to have cookies enabled
to let your settings work.
</p>

<h2>Preferred language</h2>

<p>
If you use a shortcut or search for a function, the language used
is determined by checking for the following settings. The list is
in priority order, the first is the most important. Normally you don't
need to set your preferred language, as your last seen language is
always remembered, and is a good estimate on your preferred language
most of the time.
</p>

<div class="indent">
<table border="0" cellpadding="3" cellspacing="2" class="standard">
<?php

// Data for the language settings table
$langinfo = array(
    
    "Your preferred language" =>
    $langpref,
    
    "Last seen language" =>
    (isset($_COOKIE['LAST_LANG']) ? $_COOKIE['LAST_LANG'] : "None"),
    
    "Your Accept-Language browser setting" =>
    (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : "None"),
    
    "The mirror's default language" =>
    default_language(),
    
    "Default" => "en"
);

// Write a row for all settings
foreach ($langinfo as $lin => $lid) {
    echo " <tr>\n  <td class=\"sub\">" . $lin . "</td>\n";
    echo "  <td>" . $lid . "</td>\n </tr>\n";
}

?>
</table>
</div>

<p>
These settings are only overriden in case you have passed a language
setting URL parameter or POST data to a page or you are viewing a manual
page in a particular language. In these cases, the explicit specification
overrides the language selected from the above list.
</p>

<p>
The language setting is honored when you use an
<a href="/urlhowto.php">URL shortcut</a>, when you start
a function list search on a non-manual page, when you visit
the <a href="/download-docs.php">manual download</a> or
<a href="/docs.php">language selection</a> pages, etc.
</p>

<h2>Your country</h2>

<p>
The PHP.net site and mirror sites try to detect your country
using the <a href="http://www.directi.com/?site=ip-to-country">Directi
Ip-to-Country Database</a>. This information is used to mark
the events in your country specially and to offer close mirror
sites if possible on the download page and on the mirror listing
page.
</p>

<div class="indent">
<?php
if (i2c_valid_country()) {
    echo "We detected that you are from <b>" . $COUNTRIES[$COUNTRY] . "</b>";
} else {
    echo "We were unable to detect your country";
}
?>
</div>

<h2>URL search fallback</h2>

<p>
When you try to access a PHP.net page via an URL shortcut, and
the site is unable to find that particular page, it falls back
to a documentation search, or a function list lookup, depending on
your choice. The default is a function list lookup, as most of
the URL shortcut users try to access function documentation pages.
<em>Note that documentation searches are currently [temporarily]
done through Google.</em>
</p>

<div class="indent">
Your setting: <input type="radio" name="urlsearch" value="quickref"
<?php
$type = myphpnet_urlsearch();
if ($type === MYPHPNET_URL_NONE || $type === MYPHPNET_URL_FUNC) {
    echo ' checked="checked"';
}
echo ' /> Function list search <input type="radio" name="urlsearch" value="manual"';
if ($type === MYPHPNET_URL_MANUAL) {
    echo ' checked="checked"';
}
?>
 /> PHP Documentation search
</div>

<div align="center">
<input type="submit" value="Set All Preferences">
</div>
</form>

<?php commonFooter(); ?>

<?php
/* $Id$ */
// vim: expandtab sw=4 ts=4 sts=4:

/**
 * phpMyAdmin Theme Manager
 * 2004-05-20: Michael Keck <mail_at_michaelkeck_dot_de>
 *
 * The theme manager checks the directory /themes/ for subdirectories
 * wich are the themes.
 * If you're building a new theme for PMA, your theme should include
 * make a folder theme_name/ in the directoriy /themes which includes
 * a subdirectory css/.
 * In the css-directory you should (if you want) edit the follow files:
 *    - theme_left.css.php      // includes css-styles for the left frame
 *    - theme_right.css.php     // includes css-styles for the main frame
 *    - theme_print.css.php     // includes css-styles for printing
 *
 * If you want to use default themes for left, right or print
 * so you need not to build the css-file and PMA will use it's own css.
 * If you want to use own images for your theme, you should make all
 * images (buttons, symbols, arrows) wich are included in the default
 * images directory PMA and store them into the subdirectory /img/ of
 * your theme.
 * Note:
 *     The images must be named as in the default images directory of
 *     PMA and they must have the same size in pixels.
 *     You can only use own images, if you've edit own css files.
 */
	
/**
 * We need some elements of the superglobal $_SERVER array.
 */
require_once('./libraries/grab_globals.lib.php');
/**
 * theme manager
 */
$PMA_ThemeDefault = FALSE;
$PMA_ThemeAvailable = FALSE;
if ($cfg['ThemeManager']){
    $PMA_ThemeAvailable = TRUE;
}

if ($PMA_ThemeAvailable == TRUE){ // check after default theme
    $tmp_path_default = $cfg['ThemePath'] . '/' .$cfg['ThemeDefault'];
    if (isset($cfg['ThemeDefault']) && $cfg['ThemeDefault']!='original' && is_dir($tmp_path_default)){
        // default theme is not 'original'
        $available_themes_choices[]=$cfg['ThemeDefault'];
        $PMA_ThemeDefault = TRUE;
    } else if (isset($cfg['ThemeDefault']) && $cfg['ThemeDefault']=='original'){
        // default theme is 'original'
        $PMA_ThemeDefault = TRUE;
    }
} // end check default theme

if ($PMA_ThemeAvailable == TRUE) { // themeManager is available
    if ($PMA_ThemeDefault == TRUE) {
        // we must add default theme, because it has no directory
        $available_themes_choices[]='original';
    }
    if ($handleThemes = opendir($cfg['ThemePath'])) { // check for themes directory
        while (false !== ($PMA_Theme = readdir($handleThemes))) { // get themes
            if ($PMA_Theme != "." && $PMA_Theme != ".." && $PMA_Theme!='original' && $PMA_Theme!=$cfg['ThemeDefault'] && $PMA_Theme != 'CVS') { // file check
                if (@is_dir($cfg['ThemePath'].'/'.$PMA_Theme)) { // check the theme
                    $available_themes_choices[]=$PMA_Theme;
                } // end check the theme
            } // end file check
        } // end get themes
    } // end check for themes directory
    closedir($handleThemes); 
} // end themeManger

if (!isset($pma_uri_parts)) { // cookie-setup if needed
    $pma_uri_parts = parse_url($cfg['PmaAbsoluteUri']);
    $cookie_path   = substr($pma_uri_parts['path'], 0, strrpos($pma_uri_parts['path'], '/'));
    $is_https      = (isset($pma_uri_parts['scheme']) && $pma_uri_parts['scheme'] == 'https') ? 1 : 0;
} // end cookie setup

if (isset($set_theme)) { // if user submit a theme
    setcookie('theme', $set_theme, time() + 60*60*24*30, $cookie_path, '', $is_https);
} else { // else check if user have a theme cookie
    if (!isset($_COOKIE['theme']) || empty($_COOKIE['theme'])) {
        if ($PMA_ThemeDefault == TRUE) { 
            if (basename($PHP_SELF) == 'index.php') {
                setcookie('theme', $cfg['ThemeDefault'], time() + 60*60*24*30, $cookie_path, '', $is_https);
            }
            $pmaTheme=$cfg['ThemeDefault'];
        }else{
            if (basename($PHP_SELF) == 'index.php') {
                setcookie('theme', 'original', time() + 60*60*24*30, $cookie_path, '', $is_https);
            }
            $pmaTheme='original';
        }
    } else {
        $pmaTheme=$_COOKIE['theme'];
        if (basename($PHP_SELF) == 'index.php') {
            setcookie('theme', $pmaTheme, time() + 60*60*24*30, $cookie_path, '', $is_https);
        }
    }
} // end if
?>

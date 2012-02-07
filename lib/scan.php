<?php
// jbcore
define( 'JB_REALM', $_SERVER['X_REALM'] );
require 'jbcore/jbcore.php';
// Blx Loader
require 'Blx/Loader.php';
spl_autoload_register( array( new Blx\Loader(), 'autoload' ) );

//Blx  storage manager
$storage = new Blx\Plugin\Jb\DbStorage();


$out = array();
$list = array();
// fetch all pages for current realm
foreach( $storage->listPages() as $page ) {
    // prepare URL without extension (.html)
    $tmpUrl = '/' . substr( $page['url'], 0, -5 );
    // prepare list of descirptions
    $out[$tmpUrl] = array(
        'url' => $page['url'],
        'title' => $page['title']
    );
    // prepare flat list of URLs
    $list[$tmpUrl] = $tmpUrl;
}
// prepare tree and generate output
echo prepareList( explodeTree($list, '/'), $out );

/**
 * Prepares list for Xinha's Linker plugin: \n
 * \n
 * <<node-list>> = [ <<node>, <<node>>, ...]\n
 * <<node>> = {url:"a.html",title:"A File",children:<<node-list>>}\n
 *
 * @param  array  $tree     response from explodeTree() function
 * @param  array  $desc     list of titles and URLs
 * @param  string $base     parent URL
 */
function prepareList( array $tree, array $desc, $base = '/' ) {
    $tmp = array();
    foreach( $tree as $part => $data ) {
        $tmpUrl = $base . $part;
        if ( isset( $desc[$tmpUrl] ) ) {
            $title = JBSanitize::html( $desc[$tmpUrl]['title'] );
            $url = $desc[$tmpUrl]['url'];
        } else {
            $title = $part;
            $url = $tmpUrl . '.html';
        }
        $tmp[] = sprintf(
            '{url: "%s", title: "%s", children: %s}',
            $url,
            $title,
            prepareList( $data, $desc, $base . $part . '/' )
        );
    }
    return '[' . implode(',', $tmp) . ']';
}

/**
 * Explode any single-dimensional array into a full blown tree structure,
 * based on the delimiters found in it's keys.
 *
 * @author  Kevin van Zonneveld <kevin@vanzonneveld.net>
 * @author  Lachlan Donald
 * @author  Takkie
 * @copyright 2008 Kevin van Zonneveld (http://kevin.vanzonneveld.net)
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD Licence
 * @version   SVN: Release: $Id: explodeTree.inc.php 89 2008-09-05 20:52:48Z kevin $
 * @link    http://kevin.vanzonneveld.net/
 *
 * @param array   $array
 * @param string  $delimiter
 * @param boolean $baseval
 *
 * @return array
 */
function explodeTree($array, $delimiter = '_', $baseval = false)
{
  if(!is_array($array)) return false;
  $splitRE   = '/' . preg_quote($delimiter, '/') . '/';
  $returnArr = array();
  foreach ($array as $key => $val) {
    // Get parent parts and the current leaf
    $parts  = preg_split($splitRE, $key, -1, PREG_SPLIT_NO_EMPTY);
    $leafPart = array_pop($parts);
 
    // Build parent structure
    // Might be slow for really deep and large structures
    $parentArr = &$returnArr;
    foreach ($parts as $part) {
      if (!isset($parentArr[$part])) {
        $parentArr[$part] = array();
      } elseif (!is_array($parentArr[$part])) {
        if ($baseval) {
          $parentArr[$part] = array('__base_val' => $parentArr[$part]);
        } else {
          $parentArr[$part] = array();
        }
      }
      $parentArr = &$parentArr[$part];
    }
 
    // Add the final part to the structure
    if (empty($parentArr[$leafPart])) {
      $parentArr[$leafPart] = $val;
    } elseif ($baseval && is_array($parentArr[$leafPart])) {
      $parentArr[$leafPart]['__base_val'] = $val;
    }
  }
  return $returnArr;
}

<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * FTP Settings for each site.
 * @var array
 */
$sites = array();
$sites['clips4sale'] = array(
	"url" => "ftp.clips4sale.com",
	"port" => 21,
	"video" => array(
		"path" => "/clips/"
	),
	"trailer" => array(
		"path" => "/clips_previews/uploaded_previews/"
	),
	"poster" => array(
		"path" => "/clip_images/"
	)
);
$sites['aebn'] = array(
	"url" => "aebnftp.dataconversions.biz",
	"port" => 21,
	"video" => array(
		"path" => "/"
	),
	"trailer" => array(
		"path" => "/"
	),
	"poster" => array(
		"path" => "/"
	)
);
$sites['hotmovies'] = array(
	"url" => "ftp.vod.com",
	"port" => 21,
	"video" => array(
		"path" => "/"
	),
	"trailer" => array(
		"path" => "/"
	),
	"poster" => array(
		"path" => "/"
	)
);
$sites['adultempire'] = array(
	"url" => "uploads.adultempirecash.com",
	"port" => 21,
	"video" => array(
		"path" => "/"
	),
	"trailer" => array(
		"path" => "/"
	),
	"poster" => array(
		"path" => "/"
	)
);
// var_dump(json_encode($sites[$site]));

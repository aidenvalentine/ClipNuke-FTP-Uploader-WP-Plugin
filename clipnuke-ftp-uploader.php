<?php
/**
 * ClipNuke to Remote FTP Uploader
 * Uploads Local Server Files to a specified FTP server.
 *
 * @wordpress-plugin
 * Plugin Name: ClipNuke FTP uploader
 * Description: Uploads local files to a remote FTP server.
 * Plugin URI: https://clipnuke.com
 * Author: ClipNuke
 * Author URI: mailto:aiden@xxxmultimedia.com
 * Version: 1.0.0
 * @package ClipNuke_FTP_Uploader
 * @var integer $id ClipNuke ID
 * @global string $filetype video, trailer, or poster
 * @var string|null $site clips4sale, aebn, hotmovies, or adultempire
 * @var string|null $user FTP Username
 * @var string|null $pass FTP Password
 * @var string|null $file Media URI relative to /wp-uploads/ @see {file}
 * @link http://github.com/nachox07
 * @since 1/31/2022
 * @author Aiden Valentine <aiden@xxxmultimedia.com>
 * @copyright ClipNuke.com
 * @TODO Use HTTP Authorization header to be REST compliant.
 * @TODO Make this pull user's FTP credentials directly from the database for the logged in user.
 */

namespace ClipNuke_FTP_Uploader;

/** Include Wordpress Features */
require_once("../../../wp-load.php");

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	// exit;
}

/** FTP Servers Ass. Array */
include_once("sites.php");
/** HTML Template - Header */
include_once("header.php");

class ClipNuke_FTP_Uploader {
	/**
	 * Loading all dependencies
	 * @return void
	 */
	public function load() {
		/** FTP Servers Ass. Array */
		include_once("sites.php");
		/** HTML Template - Header */
		include_once("header.php");
		// include_once 'includes/api/class-api.php';
		// include_once 'includes/db/functions.php';
		// include_once 'includes/api/functions.php';
	}
}
// class ClipNuke_FTP_Uploader {

	// Fetches filetype by CLI or Query Params.
	/**
	 * @var string $id ClipNuke Video ID. To Update on success. @see {updatePost}.
	 */
	$id = $_GET["id"] ?: getopt("id:");
	/** @var string $filetype {video, poster, or trailer}. The type of file to upload to FTP server -- some files go into specific dirs like on Clips4Sale. */
	$filetype = $_GET["filetype"] ?: getopt("filetype:");
	$site = $_GET["site"] ?: getopt("site:");
	/**
	 * FTP username from HTTP GET or CLI argument.
	 * @name $user
	 * @var string $user FTP username.
	 * TODO Use encryption and POST data instead.
	 */
	$user = $_GET["user"] ?: getopt("user:");
	/** @TODO use encryption and POST data instead. */
	$pass = $_GET["pass"] ?: getopt("pass:");
	/**
	 * @var string $file Complete path relative to wp-uploads dir. Ex. 2021/08/ffmpeg.jpg
	 * TODO Maybe support complete system path too. Ex. /var/www/html/clipnuke.com/wp-content/uploads/2021/08/ffmpeg.jpg
	 * TODO Add support for URLs
	 */
	$file = $_GET["file"] ?: getopt("file:"); // NOTE Complete path relative to wp-uploads dir. Ex.
	// $mediaId = $_GET["attachmentId"] ?: getopt("mediaId:"); // Get URL from ClipNuke.com media ID
	// print_r($_GET); // DEBUG Dump HTTP GET vars.

	/**
	 * Helper - Output String to Log
	 * @param  string $message The log message (HTML) you want to output to the page.
	 * @param string $class Optional. CSS class to assign to log message. Ex. error, success, info.
	 * @return null
	 */
	function log($message, $class="info") {
	  echo "<tr class='". $class ."'><td>".$message."</tr></td>";
	}

	log("FTP Configuration");
	log("<pre>".json_encode($sites[$site], JSON_PRETTY_PRINT)."</pre>");

	/* Restriction to execute command php ./wp-ftp-backup.php up */
	// $action = $_SERVER['argv']['1'] ?: getopt("action:");
	$action = $_GET["action"] ?: getopt("action:");
	if($action == 'up')
	{
		$ftp_server = $sites[$site]["url"];
		$ftp_user =  $user;
		$ftp_pass = $pass;
		$ftp_pass_redacted = str_repeat("*", strlen($ftp_pass));
		$wpUploadsDir = wp_get_upload_dir()["basedir"];
		$fileUrl = $wpUploadsDir . "/" . $file; // NOTE $file includes the mm/yy folders in it.
		$filePath = dirname($fileUrl);
		$filename = basename($fileUrl);
		// $sku = basename($path, ".mp4");

		/**
		* Verify the File Exists on This Server
		* @var string $filename @see {file} var.
		*/
		$fileExists = file_exists($fileUrl);
		if ($fileExists) {
			log("File Exists on Local Server: ".$fileUrl, "success");
		} else {
			log("File Not Found on Local Server: ".$fileUrl, "error");
			die;
		}
		// $path = opendir($filePath); // List all files on local server in a specific dir.

		log("Connecting to FTP Server");
		$connect = ftp_connect($ftp_server) or die ("<tr class='error'><td>"."Connection error"."</tr></td>");
		$result = ftp_login($connect, $ftp_user, $ftp_pass);

		/* Passive mode */
		ftp_pasv($connect, true);

		/* Upload start*/
		if($result)
		{
			log("FTP Login Success", "success");

			$remote_path = $sites[$site][$filetype]["path"];
			$remote_filename = $remote_path . $filename;
			$local_filename = $filePath . "/" . $filename;
			$mode = FTP_BINARY; // FTP_BINARY or FTP_ASCII
			$ftp_url = "ftp://".$ftp_user."@".$ftp_pass_redacted.":".$sites[$site]["url"].$remote_filename;

			/** Check if file exists on FTP server */
			// $dir = ftp_rawlist($connect, $remote_path); // List all files in remote dir.
			log("Saving file to:".$ftp_url);
			log("Uploading files... Please wait.");
			// var_dump($remote_filename, $local_filename, $mode);
			// if(ftp_put($connect, $remote_filename, $local_filename, $mode))
			// {
			// 	log("File uploaded successfully.", "success");
			// }

		}
		else {
			log("Connection error", "error");
		}

		ftp_close($connect);
	}
// }

include_once("footer.php");

<?php
/**
 * ClipNuke to Remote FTP Uploader
 * Uploads Local Server Files to a specified FTP server.
 *
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
 *
 * Plugin Name: ClipNuke FTP uploader
 * Description: Uploads local files to a remote FTP server.
 * Plugin URI: https://clipnuke.com
 * Author: ClipNuke
 * Author URI: mailto:aiden@xxxmultimedia.com
 * Version: 1.0.0
 * @wordpress-plugin
 */

namespace ClipNuke_FTP_Uploader;

/** Include Wordpress Features */
require_once("../../../wp-load.php");

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	// exit;
}

class ClipNuke_FTP_Uploader {
	/**
	 * Loading all dependencies
	 * @return void
	 */
	public function load() {
		/** FTP Servers Ass. Array */
		$this->config = parse_ini_file("config.ini", true);
		// include_once 'includes/api/class-api.php';
		// include_once 'includes/db/functions.php';
		// include_once 'includes/api/functions.php';
	}

	public function getParameters() {
		$vars["action"] = $_GET["action"] ?: getopt("action:");
		/**
		 * @var string $id ClipNuke Video ID. To Update on success. @see {updatePost}.
		 */
		$vars["id"] = $_GET["id"] ?: getopt("id:");
		/** @var string $filetype {video, poster, or trailer}. The type of file to upload to FTP server -- some files go into specific dirs like on Clips4Sale. */
		$vars["filetype"] = $_GET["filetype"] ?: getopt("filetype:");
		/**
		 * Site configuration to use as specified in config.ini
		 * @var string $vars["site"]
		 */
		$vars["site"] = $_GET["site"] ?: getopt("site:");
		/**
		 * FTP credentials from HTTP GET or CLI argument.
		 * @var string $user FTP username.
		 * @var string $pass FTP password.
		 * TODO Use encryption and POST data instead.
		 */
		$vars["user"] = $_GET["user"] ?: getopt("user:");
		$vars["pass"] = $_GET["pass"] ?: getopt("pass:");
		/**
		 * @var string $file Complete path relative to wp-uploads dir. Ex. 2021/08/ffmpeg.jpg
		 * TODO Maybe support complete system path too. Ex. /var/www/html/clipnuke.com/wp-content/uploads/2021/08/ffmpeg.jpg
		 * TODO Add support for URLs
		 */
		$vars["file"] = $_GET["file"] ?: getopt("file:"); // NOTE Complete path relative to wp-uploads dir. Ex.
		// $mediaId = $_GET["attachmentId"] ?: getopt("mediaId:"); // Get URL from ClipNuke.com media ID
		return $vars;
	}


  public function upload($ftp, $vars) {
		/**
		* Output a Message to the On-page Log
		* @param  string $message The log message (HTML) you want to output to the page.
		* @param string $class Optional. CSS class to assign to log message. Ex. error, success, info.
		* @return null
		*/
		function log($message, $class="info") {
			echo "<tr class='". $class ."'><td>".$message."</tr></td>";
		}

		$config = $this->config; // Sets var as alias for ClipNuke_FTP_Uploader::config
		/** Turn each array key into a var */
		foreach ($vars as $key => $value) {
			$$key = $vars[$key];
		}
		log("FTP Configuration");
		log("<pre>".json_encode($config[$site], JSON_PRETTY_PRINT)."</pre>");
		/* Restriction to execute command php ./wp-ftp-backup.php up */
		// $vars["action"] = $_SERVER['argv']['1'] ?: getopt("action:");
		if($vars["action"] == 'up')
		{
			$ftp_server = $config[$site]["url"];
			$pass_redacted = str_repeat("*", strlen($user));
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
			$result = ftp_login($connect, $user, $pass);

			ftp_pasv($connect, true); /* Passive mode */

			/* Upload start*/
			if($result)
			{
				log("FTP Login Success", "success");

				$remote_path = $config[$site]["paths"][$filetype];
				$remote_filename = $remote_path . $filename;
				$local_filename = $filePath . "/" . $filename;
				$mode = FTP_BINARY; // FTP_BINARY or FTP_ASCII

				/** Check if file exists on FTP server */
				// $dir = ftp_rawlist($connect, $remote_path); // List all files in remote dir.
				// log("Saving file to:".$ftp_url);
				$ftp_url = "ftp://".$user.":".$pass_redacted."@".$config[$site]["url"].$remote_filename;
				$_GET["debug"] ? log("Saving file to: ".$ftp_url) : log("Saving file to: ftp://".$config[$site]["url"].$remote_filename);
				log("Uploading files... Please wait.");
				// var_dump($remote_filename, $local_filename, $mode);
				if(ftp_put($connect, $remote_filename, $local_filename, $mode))
				{
					log("File uploaded successfully.", "success");
				} else {
					log("Upload failed. Please try again.", "error");
				}
			}

			ftp_close($connect);

		}
	}

	public function header($vars) {
		include_once("header.php");
	}

	public function footer() {
		include_once("footer.php");
	}
}

$ftp = new ClipNuke_FTP_Uploader();
$ftp->load();
$vars = $ftp->getParameters();
$ftp->header($vars);
$ftp->upload($ftp, $vars);
$ftp->footer();

<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<html>
	<head>
		<!-- CSS only -->
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
		<!-- JavaScript Bundle with Popper -->
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
		<style type="text/css">
		.success {
			background-color: limegreen!important;
		}
		.error {
	    background-color: red!important;
	    color: white!important;
	    font-weight: bold;
		}
		</style>
	</head>
	<body>
		<div class="container-fluid">
  		<h1>ClipNuke - FTP Uploader</h1>
  		<p><b>Usage</b>: Set all the query parameters and load page. File will be uploaded to specified server.</p>
  		<div id="inputs" class="container-fluid">
  			<h3>Inputs - Query Params / CLI Args</h3>
  			<table id="query-params" class="table table-bordered">
  				<thead class="thead-dark">
  					<tr>
  			      <th scope="col">Field</th>
  			      <th scope="col">Query Param</th>
  			      <th scope="col">Accepts</th>
  			      <th scope="col" width="30%">Value</th>
  			    </tr>
  				</thead>
  				<tbody>
  					<tr><td>ClipNuke ID</td><td>id</td><td>{string, integer}</td><td class='<?php echo $id ? "success" : ""; ?>'><?php echo $id; ?></td></tr>
  					<tr><td>Filetype</td><td>filetype</td><td>video, poster, trailer</td><td class='<?php echo $filetype ? "success" : ""; ?>'><?php echo $filetype; ?></td></tr>
  					<tr><td>Site</td><td>site</td><td>clips4sale, aebn, hotmovies, adultempire</td><td class='<?php echo $site ? "success" : ""; ?>'><?php echo $site; ?></td></tr>
  					<tr><td>User</td><td>user</td><td>{string}</td><td class='<?php echo $user ? "success" : ""; ?>'><?php echo $user; ?></td></tr>
  					<tr><td>Pass</td><td>pass</td><td>{string}</td><td class='<?php echo $pass ? "success" : ""; ?>'><?php echo str_repeat("*", strlen($pass)); ?></td></tr>
  					<tr><td>File</td><td>file</td><td>{string, URI}</td><td class='<?php echo $file ? "success" : ""; ?>'><?php echo $file; ?></td></tr>
  				</tbody>
  			</table>
  		</div>
      <div id="log" class="container-fluid">
      	<h3>Log</h3>
      	<table id="query-params" class="table table-bordered">
      		<thead class="thead-dark">
      			<tr>
      				<th scope="col">Log Entries</th>
      			</tr>
      		</thead>
      		<tbody>

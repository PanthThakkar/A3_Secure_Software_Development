<?php

$dbconn = pg_pconnect("host=$pg_host port=$pg_port dbname=$pg_dbname user=$pg_dbuser password=$pg_dbpassword") or die("Could not connect");
if ($debug) {
	echo "host=$pg_host, port=$pg_port, dbname=$pg_dbname, user=$pg_dbuser, password=$pg_dbpassword<br>";
	$stat = pg_connection_status($dbconn);
	if ($stat === PGSQL_CONNECTION_OK) {
		echo 'Connection status ok';
	} else {
		echo 'Connection status bad';
	}    
}

function run_query($dbconn, $query) {
	if ($debug) {
		echo "$query<br>";
	}
	$result = pg_execute($dbconn, '', array());
	if ($result == False and $debug) {
		echo "Query failed<br>";
	}
	return $result;
}

//database functions
function get_article_list($dbconn){
	$sql= 
		"SELECT 
		articles.created_on as date,
		articles.aid as aid,
		articles.title as title,
		authors.username as author,
		articles.stub as stub
		FROM
		articles
		INNER JOIN
		authors ON articles.author=authors.id
		ORDER BY
		date DESC";
		
	$query = pg_query_params($dbconn, $sql, array());
	
return run_query($dbconn, $query);
}



function get_article($dbconn, $aid) {

$query = pg_query_params($dbconn,
		'SELECT quote_ident(CAST($1 AS text))', array($aid));
		
	$sql= 
		"SELECT 
		articles.created_on as date,
		articles.aid as aid,
		articles.title as title,
		authors.username as author,
		articles.stub as stub,
		articles.content as content
		FROM 
		articles
		INNER JOIN
		authors ON articles.author=authors.id
		WHERE
		aid='".$aid."'
		LIMIT 1";
		
		$aid = pg_fetch_result($query, 0, 0); // safe
		
		$query = pg_query_params($dbconn, $sql, array());
		
return run_query($dbconn, $query);
}

function get_article_perms($dbconn, $aid) {

$query = pg_query_params($dbconn,
		'SELECT quote_ident(CAST($1 AS text))', array($aid));
		
	$sql= 
		"SELECT 
		articles.created_on as date,
		articles.aid as aid,
		articles.title as title,
		authors.id as author,
		articles.stub as stub,
		articles.content as content
		FROM 
		articles
		INNER JOIN
		authors ON articles.author=authors.id
		WHERE
		aid='".$aid."'
		LIMIT 1";
		
		$aid = pg_fetch_result($query, 0, 0); // safe
		
		$query = pg_query_params($dbconn, $sql, array());
		
return run_query($dbconn, $query);
}


function delete_article($dbconn, $aid) {

	$query = pg_query_params($dbconn,
		'SELECT quote_ident(CAST($1 AS text))', array($aid));
		
	$sql= "DELETE FROM articles WHERE aid='".$aid."'";
	
	$aid = pg_fetch_result($query, 0, 0); // safe
		
	$query = pg_query_params($dbconn, $sql, array());
		
	return run_query($dbconn, $query);
}

function add_article($dbconn, $title, $content, $author) {

$query = pg_query_params($dbconn,
		'SELECT quote_ident(CAST($1 AS text)), quote_ident(CAST($2 AS text)),$3;', array($title, $content, $author));
		
		$title = pg_fetch_result($query, 0, 0); // safe
		$content = pg_fetch_result($query, 0, 1); // safe
		$author = pg_fetch_result($query, 0, 2); // safe


	$stub = substr($content, 0, 30);
	$aid = str_replace(" ", "-", strtolower($title));
	
	$sql="
		INSERT INTO
		articles
		(aid, title, author, stub, content) 
		VALUES
		('$aid', '$title', '$author', '$stub', '$content')";
		
		
	$query = pg_query_params($dbconn, $sql, array());
		
	return run_query($dbconn, $query);
}

function update_article($dbconn, $title, $content, $aid) {

$query = pg_query_params($dbconn,
		'SELECT quote_ident(CAST($1 AS text)), quote_ident(CAST($2 AS text)), quote_ident(CAST($3 AS text));', array($title, $content, $aid));
		
		$title = pg_fetch_result($query, 0, 0); // safe
		$content = pg_fetch_result($query, 0, 1); // safe
		$aid = pg_fetch_result($query, 0, 2); // safe
		
	$sql=
		"UPDATE articles
		SET 
		title='$title',
		content='$content'
		WHERE
		aid='$aid'";
		
	$query = pg_query_params($dbconn, $sql, array());
	
	
	return run_query($dbconn, $query);
}

function authenticate_user($dbconn, $username, $password) {
	$query = pg_query_params($dbconn,
		'SELECT quote_ident(CAST($1 AS text)), quote_ident(CAST($2 AS text));', 			array($username, $password));
		
		$username = pg_fetch_result($query, 0, 0); // safe
		$password = pg_fetch_result($query, 0, 1); // safe
	
	$sql = "SELECT
		authors.id as id,
		authors.username as username,
		authors.password as password,
		authors.role as role
		FROM
		authors
		WHERE
		username='$username'
		AND
		password='$password'
		LIMIT 1";
	
	$query = pg_query_params($dbconn, $sql, array());
	
	return run_query($dbconn, $query);
}	
?>

<?php

include_once 'sql.php';

function arrayToString($arr){
	$str = "";
	for( $i = 0; $i < count($arr); $i++ ){
		if( $i > 0)
			$str .= ", ";
		$str .= $arr[$i];
	}
	
	return $str;
}

/**
 * @method Returns the possiblie navigation symbols
 * @param int $articleId
 */
function getSymbols($con, $symbolId){

	$symbolId = intval($symbolId);
	$query = "SELECT symbols.id, symbols.name, symbols.icon FROM symlinks 
			JOIN symbols
			ON symlinks.target = symbols.id
			WHERE source = $symbolId";

	$symbols = Array();
	if ($result = $con->query($query)) {
		while($obj = $result->fetch_object()){
	            $symbols[]=$obj;
	     } 
	    $result->close();
	}

	return $symbols;

}

/**
 * @method Returns id, text, symbol and book number of an article
 * @param int $articleId
 */
function getArticle($con, $articleId){
	
	$articleId = intval($articleId);
	
	$query = "SELECT articleid AS id, text, symbol, book  
			FROM articles 
			WHERE articleid = $articleId";
	
	if ($result = $con->query($query)) {
		return $result->fetch_object();
	    $result->close();
	}

	return null;
}

/**
 * Returns the categories of an article
 * @param mysqli $con
 * @param int $articleId
 * @return category array
 */
function getCategories($con, $articleId){
	
	$articleId = intval($articleId);
	
	$query = "SELECT nodeid FROM articlenodes WHERE articleid = $articleId";

	$catIds = Array();
	if ($result = $con->query($query)) {
		while($obj = $result->fetch_object()){
	            $catIds[]=intval($obj->nodeid);
	     } 
	    $result->close();
	}

	return $catIds;
}

/**
 * Returns the categories and directly linked categories of an article
 * @param mysqli $con
 * @param int $articleId
 * @return array
 */
function getCategoriesEnv($con, $articleId){
	
	$catIds = getCategories($con, $articleId);
	$catIdString = arrayToString($catIds);

	$query = "SELECT * FROM nodes ".
		"JOIN links ".
		"ON nodes.nodeid = links.source OR nodes.nodeid = links.target ".
		"WHERE links.source IN ($catIdString) OR links.target IN ($catIdString)";
	
	$retCats = Array();
	if ($result = $con->query($query)) {
		while($obj = $result->fetch_object()){
			$retCats[]=intval($obj->nodeid);
		}
		$result->close();
		
		return array_values(array_unique($retCats, SORT_NUMERIC));
	}
	
	return $retCats;
}

/**
 * Returns the number of articles with the same book Id
 * @param mysqli $con
 * @param int $bookId
 * @return number
 */
function getBookCount($con, $bookId){

	$bookId = intval($bookId);
	
	$query = "SELECT count(book) AS bookCount FROM articles " .
			"WHERE book = $bookId"; // ? doesn't work...however?

	if ($result = $con->query($query)) {
		$obj = $result->fetch_object();
		$result->close();
		
		return intval($obj->bookCount);
	}
	
	return 0;	
}

/**
 * Returns total article count
 * @param mysqli $con
 * @return number
 */
function getArticleCount($con){

	$query = "SELECT count(*) AS total FROM articles";
	if ($result = $con->query($query)) {
		$obj = $result->fetch_object();
		$result->close();
	
		return intval($obj->total);
	}
	
	return 0;
}

/**
 * returns nodes connected with article
 * @param mysqli $con
 * @param int $articleId
 * @return array:nodes
 */
function getNodes($con, $articleId){

	$articleId = intval($articleId);
	
	$query = "SELECT nodes.nodeid, nodes.name, x, y " .
	"FROM articlenodes " .
	"JOIN nodes " .
	"ON articlenodes.nodeid = nodes.nodeid " .
	"WHERE articleid = $articleId";

	$nodes = Array();
	if ($result = $con->query($query)) {
		while($obj = $result->fetch_object()){
			$nodes[]=$obj;
		}
		$result->close();
		
		return $nodes;
	}
	
	return $nodes;
}

/**
 * 
 * @param mysqli $con
 * @param article $article
 * @param int $lastSymbol
 * @param array $lastArticles
 * @param bool $increment increment article count (default=true)
 * @return article
 */
function addArticleInfo($con, $article, $lastSymbol, $lastArticles, $increment=true){
	
	$article->bookCount = getBookCount($con, $article->book);
	$article->totalCount = getArticleCount($con);
	$article->nodes = getNodes($con, $article->id);
	$article->symbols = getSymbols($con, $lastSymbol);
	$article->lastArticles = $lastArticles;
	$article->lastArticles[] = intval($article->id);
	
	//increment article count
	if( $increment ){
		$query = "UPDATE articles SET count=count+1 WHERE articleid=$article->id";
		if (!$result = $con->query($query)) {
			error_log("Counter increment of article $article failed!");
		}
	}
	
	return $article;
}

/**
 * 
 * @param mysqli $con
 * @return multitype:unknown |NULL
 */
function getThumbs($con){
	
	$query = "SELECT articles.*, AVG(nodes.x) AS x, AVG(nodes.y) AS y
			FROM articles
			JOIN articlenodes
			ON articles.articleid = articlenodes.articleid
			JOIN nodes
			ON nodes.nodeid = articlenodes.nodeid
			WHERE active = 1 
			GROUP BY articles.articleid";
	
	$thumbs = Array();
	if( $result = $con->query($query) ){
		while( $thumb = $result->fetch_object() ){
			$thumb->articleid = intval($thumb->articleid);
			$thumb->x = intval($thumb->x);
			$thumb->y = intval($thumb->y);
			$thumbs[] = $thumb;
		}
		return $thumbs;		
	}
	return null;
}
?>
<?php

use MediaWiki\MediaWikiServices;




class MyRelatedArticle extends SpecialPage {





    
	function __construct() {
		parent::__construct( 'MyRelatedArticle' );
        $this->mIncludable = true;
	}




	function execute( $par ) {
		global $wgOut;


$homepage = file_get_contents('https://kianting.info/wiki/api.php?action=query&format=json&prop=categories&titles='.$par);
$jsonResult = json_decode($homepage, true);

$categoryArray = array();

foreach ($jsonResult["query"]["pages"] as $k1 => $v1){
    foreach($v1["categories"] as $k2 => $v2){
        array_push($categoryArray, $v2["title"]);
    }
}

$articleArray = array();

foreach ($categoryArray as $i => $category){
    $query = file_get_contents('https://kianting.info/wiki/api.php?action=query&cmtype=page&list=categorymembers&cmtitle='.$category.'&format=json');
    $jsonResult = json_decode($query, true);
    foreach ($jsonResult["query"]["categorymembers"] as $i => $article){
        array_push($articleArray, $article["title"]);
    }
}



if(count($articleArray)<5){
$parentCategoryArray = array();


foreach ($categoryArray as $i => $category){
    $query =  file_get_contents('https://kianting.info/wiki/api.php?action=query&format=json&prop=categories&titles='.$category);
    $jsonResult = json_decode($query, true);

    foreach ($jsonResult["query"]["pages"] as $k1 => $v1){
    foreach($v1["categories"] as $k2 => $v2){
        array_push($parentCategoryArray, $v2["title"]);
    }
}
}

foreach ($parentCategoryArray as $i => $category){
    $query = file_get_contents('https://kianting.info/wiki/api.php?action=query&cmtype=page&list=categorymembers&cmtitle='.$category.'&format=json');
    $jsonResult = json_decode($query, true);
    foreach ($jsonResult["query"]["categorymembers"] as $i => $article){
        array_push($articleArray, $article["title"]);
    }
}
}



$articleArray = array_diff($articleArray, array($par));


$selectedArray = array();




if (count($articleArray) > 5){

foreach( array_rand($articleArray, 5) as $key ) {
    array_push($selectedArray, $articleArray[$key]);
}
}else{
    $selectedArray = $articleArray;
}

 # addWikiMsg

$outputContent = "== 關聯條目 ==\n";

foreach ($selectedArray as $i => $article){
    $outputContent = $outputContent. "* [[" . $article . "]]\n";
}


$wgOut->addWikiTextAsInterface($outputContent);
# echo( $json_result["query"]["pages"][644]);
#$wgOut->addHTML($outputContent);
	}





    
}



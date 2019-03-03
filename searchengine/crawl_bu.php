<?php
include ("config.php");
include ("classes/DomDocumentParser.php");

$alreadyCrawled = array();
$crawling = array();
$alreadyFoundImages = array();

function linkExists($url){
  global $con;

  if($con){

    $query = $con->prepare("SELECT * FROM sites WHERE url = :url");

    $query->bindParam(":url", $url);
    $query->execute();
    return $query->rowCount() != 0; 
  }
}

function insertLink($url, $title, $description, $keywords){
  global $con;

  if($con){

    $query = $con->prepare("INSERT INTO sites(url, title, description, keywords) VALUES(
    :url, :title, :description, :keywords )");

    $query->bindParam(":url", $url);
    $query->bindParam(":title", $title);
    $query->bindParam(":description", $description);
    $query->bindParam(":keywords", $keywords);

    return $query->execute();
  }
}

function insertImage($siteUrl, $imageUrl, $alt, $title){
  global $con;

  if($con){

    $query = $con->prepare("INSERT INTO images(siteUrl, imageUrl, alt, title) VALUES(
    :siteUrl, :imageUrl, :alt, :title )");

    $query->bindParam(":siteUrl", $siteUrl);
    $query->bindParam(":imageUrl", $imageUrl);
    $query->bindParam(":alt", $alt);
    $query->bindParam(":title", $title);

    $query->execute();
    // return $query->execute();
  }
}

function createLink($src, $url){
  $scheme = parse_url($url)["scheme"];
  $host = parse_url($url)["host"];
  // echo "SRC: $src<br>";
  // echo "URL: $url<br>";
  if(substr($src, 0, 2) == "//"){
    $src = $scheme. ":" . $src;
  }
  elseif(substr($src, 0, 1) == "/"){
    $src = $scheme. "://" . $host . $src;
  }
  elseif(substr($src, 0, 2) == "./"){
    $src = $scheme . "://" . $host . dirname(parse_url($url)["path"]) . substr($src, 1);
  }
  elseif(substr($src, 0, 3) == "../"){
    $src = $scheme . "://" . $host . "/" . $src;
  }
  elseif( (substr($src, 0, 5) != "https") && (substr($src, 0, 4) != "http") ){
    $src = $scheme . "://" . $host . "/" . $src;
  }

  return $src;

}

function getDetails($url){
  global $alreadyFoundImages;
  $parser = new DomDocumentParser($url);

  $titleArray = $parser->getTitleTags();
  if( (sizeof($titleArray)  == 0 ) || ( $titleArray->item(0) == NULL ) ){
    return;
  }

  $title = $titleArray->item(0)->nodeValue;
  $title = str_replace("\n", "", $title);

  if($title == ""){
    return;
  }

  $description = "";
  $keywords = "";

  $metasArray = $parser->getMetaTags();
  foreach($metasArray as $meta){

    if($meta->getAttribute("name") == "description"){
     $description = $meta->getAttribute("content");
    }

    if($meta->getAttribute("name") == "keywords"){
      $keywords = $meta->getAttribute("content");
     }
  }

  $description = str_replace("\n", "", $description);
  $keywords = str_replace("\n", "", $keywords);
  // echo "URL: $url, title: $title, desc: $description, keywords: $keywords<br>";
  if(linkExists($url)){
    echo "$url already exists<br>";
  }elseif( insertLink($url, $title, $description, $keywords)){
    echo "$url success<br>";
  }else{
    echo "$url failed<br>";
  }

  $imageArray = $parser->getImages();
  foreach($imageArray as $image){
    $src = $image->getAttribute("src");
    $alt = $image->getAttribute("alt");
    $title = $image->getAttribute("title");

    if(!$title && $alt){
      $src = createLink($src, $url);

      if(!in_array($src, $alreadyFoundImages)){
        $alreadyFoundImages[] = $src;

        insertImage($url, $src, $alt, $title);        
      }

    }
  }
 
}

function followLinks($url){
  global $alreadyCrawled;
  global $crawling;

  $parser = new DomDocumentParser($url);

  $linkList = $parser->getLinks();

  foreach($linkList as $link ){
    $href = $link->getAttribute("href");

    if(strpos($href, "#") !== false){
      continue;
    }
    elseif(substr($href, 0, 11) == "javascript:"){
      continue;
    }
    $href = createLink($href, $url);

    if(!in_array($href, $alreadyCrawled)){
      $alreadyCrawled[] = $href;
      $crawling[] = $href;

      getDetails($href);
      //insert href into db
    }
    // else return;

    // echo $href . "<br>";
  }
  array_shift($crawling);
  foreach($crawling as $site){
    followLinks($site);
  }
}

// $startUrl = "https://www.google.com/search?source=hp&ei=JydzXI_nC4KWsgW_x5go&q=dog&btnK=Google+Search&oq=dog&gs_l=psy-ab.3..0l4j0i131l2j0l4.44836727.44837371..44837848...1.0..0.91.253.4......0....1..gws-wiz.....0..35i39.tIUMkUF5p9M";
$startUrl = "https://www.bbc.com/";

followLinks($startUrl);

?>
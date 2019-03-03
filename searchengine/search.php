<?php
include "config.php";
include "classes/SitesResultsProvider.php";
// $term = '';
if(isset($_GET['term'])){
  $term = $_GET['term'];
} else {
  // echo "You must enter search term";
}
// $type = '';
// if(isset($_GET['type'])){
//   $type = $_GET['type'];
// } else {
//   $type = 'sites';
// }

$type = isset($_GET['type']) ? $_GET['type'] : "sites";
$page = isset($_GET['page']) ? $_GET['page'] : 1;

?>
<!DOCTYPE html>
<html>
  <head>
    <title>Goggle</title>

    <link rel="stylesheet" type="text/css" href="assets/css/style.css" />
  </head>
  <body>
    <div class="wrapper">
      <div class="header">
        <div class="headerContent">
          <div class="logoContainer">
            <a href="index.php">
            <img src="assets/images/goggle.png" alt="">
            </a>
          </div>
          <div class="searchContainer">
            <form action="search.php" method="GET">
              <div class="searchBarContainer">
                <input class="searchBox" type="text" name="term" value="<?php echo $term; ?>" />
                <button class="searchButton">
                  <img src="assets/images/search.png" alt="">
                </button>
              </div>
            </form>
          </div>
        </div>

        <div class="tabsContainer">
          <ul class="tabList">
            <li class="<?php echo $type == 'sites' ? 'active' : '' ?>">
              <!-- why not this?
              <a href="search.php?term=sites">Sites</a> -->
              <a href='<?php echo "search.php?type=sites"; ?>'>Sites</a>
            </li>

            <li class="<?php echo $type == 'images' ? 'active' : '' ?>">           
              <a href='<?php echo "search.php?type=images"; ?>'>Images</a>
            </li>
            
          </ul>
        </div>
      </div>
    <div class="mainResultsSection">
      <?php
        $resultsProvider = new SitesResultsProvider($con);       
        $pageLimit = 20;

        $numResults =  $resultsProvider->getNumResults($term);
        echo "<p class='resultsCount'>$numResults results found</p>";
        
        echo $resultsProvider->getResultsHtml($page, $pageLimit, $term);
       
      ?>
      <!-- <p class="resultsCount">50 results found</p> -->
       
    </div>

    </div>
  </body>
</html>


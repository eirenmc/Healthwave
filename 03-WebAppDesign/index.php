<?php 
   session_start();
?>
<!DOCTYPE html>
    <head>
		<title> Healthwave Exercise 03 </title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<link href="styles.css" type="text/css" rel="stylesheet">
	
    </head>
    <body>

        <div class="container">
			<center>
				<h2> Medication Search: </h2>
			</center>
            <form action="healthwave03.php" method="GET">
				<div class="row">  
				  <div class="col-lg-12">
					<div class="input-group">
					  <input type="text" name="search_value" class="form-control" placeholder="Search for...">
					  <span class="input-group-btn">
						<button class="btn btn-default" type="submit" name="submit">Search</button>
					  </span>
					</div>
				  </div>
				</div>
            </form>

		<?php
				global $conn;
				$medTotal = array();

				if(isset($_SESSION["medicationFound"])){
					$medicationFound = $_SESSION["medicationFound"];
				}else{
					$_SESSION['medicationFound'] = array();
					$medicationFound = $_SESSION["medicationFound"];
				}


				if(isset($_SESSION["medTotal"])){
					$medTotal = $_SESSION["medTotal"];
				}else{
					$_SESSION['medTotal'] = array();
					$medTotal = $_SESSION["medTotal"];
				}
                
                $medicationSearchValue = $_GET["search_value"];

                if(!empty($medicationSearchValue)){
                    //Cleans the values entered in the inputs and then searches for the medication
                    $medicationSearchValue = clean_input($medicationSearchValue);
                    
                    if(!empty($medicationSearchValue)){

                        $medicationSearchValue = strtolower($medicationSearchValue);
                        searchForMedicine($medicationSearchValue);
                    }else{
                        $medicationSearchValue = NULL;
    					echo "<h2>No Valid Search Value Set<h2>".$medicationSearch;
                    }
                }else{
                    $medicationSearchValue = NULL;
					echo "<h2>No Search Value Set<h2>".$medicationSearch;
                }

                //Function that cleans data and strips out tags and code so inputs can't be code
                function clean_input($data){
                    
                    $data = strip_tags($data);
                    $data = trim($data);
                    $data = htmlentities($data);
                    $data = htmlspecialchars($data);
                    $data = strIpslashes($data);

                    return $data;
                }
				
				function searchForMedicine($medicationSearchValue){
					echo "<hr><br>";
					echo "<table><tr><th>Medicine</th><th>Pack Size</th><th>Price</th></tr>";

					try{
	                    $conn = new PDO('mysql:host=localhost; dbname=PLACEHOLDER', 'PLACEHOLDER', 'PLACEHOLDER');
                        $conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
																		
						$searchMedicine = $conn->prepare("SELECT * FROM Medicines WHERE product_name='$medicationSearchValue'");

						//Execute
						$searchMedicine->execute();
						$medicine = $searchMedicine->fetchAll(PDO::FETCH_ASSOC);
						
						if((!empty($searchMedicine)) && ($searchMedicine != NULL)){
							$medicationFound = $_SESSION["medicationFound"];
							for($i=0; $i < count($medicine); $i++){
								$row = $medicine[$i];
								echo "<tr><td>".$row['product_name']."</td><td>".$row['pack_size']."</td><td>".$row['price']."</td></tr>";					
								array_push($_SESSION['medTotal'], $row['price']);
							}
							//Pushing already searched medicines into a session array so each page load contains previously searched during the current session
							array_push($_SESSION['medicationFound'], $medicationSearchValue);
						}
					}catch(PDOException $e){
						//shows error messages
						echo 'ERROR: '.$e -> getMessage();
					}



					if(isset($_SESSION["medicationFound"])){
						$arrVals = $_SESSION["medicationFound"];
						try{  
							for($i=0; $i < count($arrVals); $i++){
								
								
								$conn = new PDO('mysql:host=localhost; dbname=PLACEHOLDER', 'PLACEHOLDER', 'PLACEHOLDER');
                        		$conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
								$searchFoundMeds = $conn->prepare("SELECT * FROM Medicines");
								$searchFoundMeds->execute();
								$prevMed = $searchFoundMeds->fetchAll(PDO::FETCH_ASSOC);
							//	$arrVals = $medicationFound[$prevMed];
								$row = $prevMed[$i];
								echo "<tr><td>".$row['product_name']."</td><td>".$row['pack_size']."</td><td>".$row['price']."</td></tr>";														
								array_push($_SESSION['medTotal'], $row['price']);
							}	
						}catch(PDOException $e){
							//shows error messages
							echo 'ERROR: '.$e -> getMessage();
						}
					}
					
					echo "</table>";
					$medSumTotal = array_sum($medTotal);
					echo "<div id='total'><center><b>Total:".$medSumTotal."</b></center></div>";
												
				}		
                
		?>
          
        </div>

    </body>
</html>
<?php 
	//Start all sessions
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

				//Checking if sessions have been set for the previously searched medications
				if(isset($_SESSION["medicationFound"])){
					$medicationFound = $_SESSION["medicationFound"];
				}else{
					$_SESSION['medicationFound'] = array();
					$medicationFound = $_SESSION["medicationFound"];
				}

				//Checking if sessions have been set for the medication prices
				if(isset($_SESSION["medTotal"])){
					$medTotal = $_SESSION["medTotal"];
				}else{
					$_SESSION['medTotal'] = array();
					$medTotal = $_SESSION["medTotal"];
				}
				
				//Storing GET value into a variable
                $medicationSearchValue = $_GET["search_value"];

                if(!empty($medicationSearchValue)){
                    //Cleans the values entered in the inputs and then searches for the medication
                    $medicationSearchValue = clean_input($medicationSearchValue);
					
					//This is a second check that the value is not empty, this is in case a html entity etc was entered
					//and got stripped out, this makes sure there is still a value before preceeding, if not the search
					//query was not valid eg. putting in <h2> would be stopped at this if but if it has <h2>Hello World</h2>
					//it will leave the text and proceed
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
					// Creating the table in browser
					echo "<hr><br>";
					echo "<table><tr><th>Medicine</th><th>Pack Size</th><th>Price</th></tr>";

					// This try is for retrieval of data that was just entered and submitted in the search box
					try{
						//DB connection and prepared sql statement
	                    $conn = new PDO('mysql:host=localhost; dbname=PLACEHOLDER', 'PLACEHOLDER', 'PLACEHOLDER');
                        $conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);																		
						$searchMedicine = $conn->prepare("SELECT * FROM Medicines WHERE product_name='$medicationSearchValue'");

						//Execute and fetching results
						$searchMedicine->execute();
						$medicine = $searchMedicine->fetchAll(PDO::FETCH_ASSOC);
						
						//If the search is valid and returns values, it creates table rows
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

					//This is used for retrieval of data of previosuly searched which have been saved into a session array
					if(isset($_SESSION["medicationFound"])){
						$arrVals = $_SESSION["medicationFound"];
						try{
							//Looping through session array  
							for($i=0; $i < count($arrVals); $i++){
								$conn = new PDO('mysql:host=localhost; dbname=PLACEHOLDER', 'PLACEHOLDER', 'PLACEHOLDER');
                        		$conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
								$searchFoundMeds = $conn->prepare("SELECT * FROM Medicines");
								$searchFoundMeds->execute();
								$prevMed = $searchFoundMeds->fetchAll(PDO::FETCH_ASSOC);
								$row = $prevMed[$i];

								//Displays results as table rows and pushes prices into session array medTotal
								echo "<tr><td>".$row['product_name']."</td><td>".$row['pack_size']."</td><td>".$row['price']."</td></tr>";														
								array_push($_SESSION['medTotal'], $row['price']);
							}	
						}catch(PDOException $e){
							//shows error messages
							echo 'ERROR: '.$e -> getMessage();
						}
					}
					echo "</table>";

					//Generating the medTotal
					$medSumTotal = array_sum($medTotal);
					echo "<div id='total'><center><b>Total:".$medSumTotal."</b></center></div>";							
				}		     
		?>
        </div>
    </body>
</html>
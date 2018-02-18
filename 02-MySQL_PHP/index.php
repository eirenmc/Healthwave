<!DOCTYPE html>
    <head>
		<title> Healthwave Exercise 02 </title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<link href="styles.css" type="text/css" rel="stylesheet">
	
    </head>
    <body>
    
    <?php
        $dataQueryDrug = "Panadol";
        $dataQueryDrug = clean_input($dataQueryDrug);
        searchForDrugCustomers($dataQueryDrug);

        //Clean and strip data of anything except plain text
        function clean_input($data){         
            $data = strip_tags($data);
            $data = trim($data);
            $data = htmlentities($data);
            $data = htmlspecialchars($data);
            $data = strIpslashes($data);
            return $data;
        }

        //Function for searching of customers using a specified drug
        function searchForDrugCustomers($dataQueryDrug){
            try{
                //Placeholder is where the relevant info for DB connection is required
                $conn = new PDO('mysql:host=localhost; dbname=PLACEHOLDER', 'PLACEHOLDER', 'PLACEHOLDER');
                $conn -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                                                
                $searchQuery = $conn->prepare("SELECT c.id, c.first_name, c.last_name AS Customers FROM customers c JOIN prescriptions p ON c.id = p.customer_id JOIN prescriptionItems psi on p.id = psi.prescription_id JOIN drugs d ON psi.drug_id = d.id WHERE d.name='$dataQueryDrug'" );

                //Execute
                $searchQuery->execute();
                $customers = $searchQuery->fetchAll(PDO::FETCH_ASSOC);
            
                echo "<center><h2>Customers</h2>"
                echo "<table><tr><th>ID</th><th>First Name</th><th>Last Name</th></tr>";
                for($i=0; $i < count($customers); $i++){
                    $row = $customers[$i];
                    echo "<tr><td>".$row['id']."</td><td>".$row['first_name']."</td><td>".$row['last_name']."</td></tr>";	
                }
                echo "</table></center>"
            }catch(PDOException $e){
                //shows error messages
                echo 'ERROR: '.$e -> getMessage();
            }
        }
    ?>

    </body>
</html>
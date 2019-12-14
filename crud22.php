<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <title>Crud22!</title>
  </head>
  <body>
  <div class="container">
<?php
    session_start();
    

	error_reporting( E_ALL );
	ini_set( 'display_errors', 1 );
		
	//print_r($_POST);
	  
$conn = include('pdo_connect.php'); 
// echo 'connected to database';
	  
if(!$conn) //verify database connection
	
{
	die('cannot display quotes due to server error');
} //end of connection 

//setting variables for data update and or edit section 
$id=0;
$modify=FALSE;
$title='';
$entry='';
// $_POST=array();

//if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Handle the form.
if(isset($_POST['save'])) {
    //	echo '<pre>';
    //	var_dump($_SERVER);
    //	echo '</pre>';
    
        // Validate the form data:
        $problem = FALSE;
        if (!empty($_POST['title']) && !empty($_POST['entry'])) {
            $title = trim(strip_tags($_POST['title']));
            $entry = trim(strip_tags($_POST['entry']));
            $_SESSION['message']='Successfully saved insert';
            $_SESSION['type']='success';
            
        } else {
            print '<p style="color: red;">Please submit both a title and an entry.</p>';
            $problem = TRUE;
        }
    
        if (!$problem) {
    
            // Connect and select:
            
            include('pdo_connect.php');		
    
            $insert = $dbh->prepare("INSERT INTO entries(title, entry, date_entered) VALUES (:title, :entry, NOW())");
            $insert->bindParam(':title', $title);
            $insert->bindParam(':entry', $entry);

            header("location: crud22.php");
            
            // Execute the insertion:
            if ($insert->execute()) {
                
               // print '<p><br>The blog entry has been added!</p>';
                
                
            } else {
                print '<p style="color: red;">WEBD166: Insert Failed!</p>';
            }
           
            
           //$dbh = NULL; // Close the connection.
        } // No problem!
        // echo '<pre>';
        // print_r($_POST);
        // echo '</pre>';
    
    
    } // End of form submission IF.

    if(isset($_POST["delete"])) {
        // echo 'inside delete isset';
        $id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
        if ($id > 0) {
            $query = "DELETE FROM entries WHERE id = :id LIMIT 1";
            $result=$dbh->prepare($query);
            $result->bindParam(":id", $id);
            $id = $_POST["id"];
            $result->execute();
            // $_SESSION['message']='Delete was successful';
            $_SESSION['type']='danger';
            //echo $result->rowCount() . " " . "Row(s) deleted <br>";	
            // echo "ok";
        } else {
            echo "delete err";
        }
    
    }


    if(isset($_GET['edit'])){

        $id=$_GET['edit'];
        $modify=TRUE;
        $result = $dbh->query("SELECT * FROM entries WHERE id=$id");
		  
			if($row = $result->fetch(PDO::FETCH_ASSOC)) {
				$id = $row['id'];
				$title=$row['title'];
                $entry=$row['entry'];
                
                $_SESSION['message']='Form was populated';
                $_SESSION['type']='info';
				
		
		}else {
            echo "edit error";
        }

    }


  if(isset($_POST['update'])){

        $id = $_POST['id'];
        $title = $_POST['title'];
        $entry = $_POST['entry'];


    $update  = $dbh->prepare("UPDATE entries SET title='$title', entry='$entry' WHERE id={$_POST['id']}");
    // echo 'past dbh prepare';
    $update->bindParam(':title', $title);
    $update->bindParam(':entry', $entry);
    $update->bindParam(':id', $id);
    

    //var_dump($update);
    $_SESSION['message']='Update was successful';
    $_SESSION['type']='success';


    header("location: crud22.php");

    // Report on the result:
    if ($update->execute()) {
        // print '<p>The blog entry has been updated.</p>';
        echo '<script type=text/JavaScript>$(".myform")[0].reset()<script>';
       
    } else {
        print '<p style="color: red;">WEBD166 Edit Update Failed</p>';
    }
    

} // No problem!   
  

    
?>


<div class="wrapper">

    <div class="row justify-content-center">
        <h3>All in One C.R.U.D. Script</h3>
    </div>

    <div class="row justify-content-center">
    <h4>(Create, Read, Update and Delete)</h4>
    </div>
        
        <div class="row justify-content-center">
            <section>
            <table class="table table-striped">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Title </th>
                        <th scope="col">Description</th>
                        <th scope="col">Delete</th>
                        <th scope="col">Edit</th>
    
                    </tr>
    
<?php

include('pdo_connect.php');
$query = $dbh->query("SELECT * FROM entries ORDER BY date_entered DESC");

// echo 'past query all entries desc';
while ($row = $query->fetch(PDO::FETCH_ASSOC))
	{
		echo '<tr>
		<td class="id">'.$row['id'].'</td>
		<td class="title">'.$row['title'].'</td>
		<td class="entry">'.$row['entry'].'</td>	
		<td class= ""><form action="" method="POST">
		<input type="hidden" name="id" data-id="'.$row['id'].'" value= "'.$row['id'].'">
        <input type="submit" class="deletedata btn btn-sm btn-danger" value="Delete" name="delete" onclick="return confirm(\'Are you sure you want to delete row: ' . $row["id"] . '?\')"></form></td>
        
        <td>        
        <a href="crud22.php?edit='.$row['id'].'" class="btn btn-sm btn-info">Edit</a>
        </td>
';
	}//end of query

?>

<?php 

if (isset($_SESSION['message'])): ?>

<div class="alert alert-<?php $_SESSION['msg_type']?>">
<?php echo $_SESSION['message'];
        unset($_SESSION['message']) ?>
</div>
<?php endif ?>





</tr>
</table>
</section>
</div>


 <div class="row justify-content-center">   
    <form class="myForm" id="form" action="crud22.php" method="POST" >
    <input type="hidden" name="id" data-id="'.$row['id'].'" value="<?php echo $id;?>">
      <div class="form-group">
            <label>Title</label>
            <input type="text" class="form-control" id="title" name="title" value="<?php echo $title;?>" placeholder="Enter Title">
        </div>
        <div class="form-group">
            <label>Entry</label>
            <input type="text" class="form-control" id="entry" name="entry" value="<?php echo $entry;?>" placeholder="Enter Entry">
        </div>
        <div class="form-group">
        <?php if ($modify == true):?>
            <button type="submit" class="btn btn-info" id="update" name="update" >Update</button>

        <?php else: ?>
            <button type="submit" class="btn btn-primary" id="save" name="save" >Save</button>
        <?php endif; ?>
        
        </div>   
    </form>
</div>
</div>
    

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </div>
  </body>
</html>
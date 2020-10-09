<?php
require_once('../config.php');

try {
    $conn = new PDO("mysql:host=".$CFG->dbhost.";dbname=".$CFG->dbname, $CFG->dbuser, $CFG->dbpass);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully" . "<br>";
  } catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }

//Instantiating variables
$descriptions = array();
$additionals = array();
$answers = [];

$questions = array();
$questions_only = array();

$sorted_questions;

$choice_type_questions;
//$order_type_questions = array();
//$drag_and_drop_type_questions = array();

$sql_additionals = 'SELECT user_id, additionals, description, response, content_id FROM moodle.mdl_hvp_xapi_results WHERE interaction_type != "compound" ';

$sql_choice_type_questions = 'SELECT json_content,main_library_id, default_language FROM moodle.mdl_hvp WHERE main_library_id = 33';
//$sql_order_type_questions = 'SELECT json_content FROM moodle.mdl_hvp WHERE default_language = "en" AND main_library_id = 26';
//$sql_drag_and_drop_type_questions = 'SELECT json_content FROM moodle.mdl_hvp WHERE default_language = "en" AND main_library_id = 25';
//$sql_image_pair_type_questions = 'SELECT json_content FROM moodle.mdl_hvp WHERE default_language = "en" AND main_library_id = 103';
//$sql_image_map_single_type_questions = 'SELECT json_content FROM moodle.mdl_hvp WHERE default_language = "en" AND main_library_id = 92';
//$sql_image_map_multiple_type_questions = 'SELECT json_content FROM moodle.mdl_hvp WHERE default_language = "en" AND main_library_id = 102';

//$sql_questions = 'SELECT id,json_content,default_language, main_library_id FROM moodle.mdl_hvp';
//$sql_questions_only = 'SELECT json_content FROM moodle.mdl_hvp WHERE default_language = "en" ';

//SINGLE/MULTIPLE
foreach ($conn->query($sql_choice_type_questions) as $row) {
  $question = json_decode($row['json_content'],true);
  $choice_type_questions[] = array(
    "json_content" => $question,
    "language" => $row['default_language'],
    "main_library_id" => $row['main_library_id'],
  );
}
//var_dump($choice_type_questions);

//ORDER
//foreach ($conn->query($sql_order_type_questions) as $row) {
//$question = json_decode($row['json_content'],true);
//$order_type_questions[] = $question;
//}
//CLASSIFICATION
//foreach ($conn->query($sql_drag_and_drop_type_questions) as $row) {
//  $question = json_decode($row['json_content'],true);
//  $drag_and_drop_type_questions[] = $question;
//}

//SINGLE/MULTIPLE WRITE OUT
foreach ($choice_type_questions as $key5 => $value5) {
  //echo $key5 . " - " . $value5 . "<br>";
  foreach ($value5 as $key4 => $value4) {
    //echo $key4 . " - " . $value4 . "<br>";
    foreach ($value4 as $key3 => $value3) {
      //echo $key3 . " - " . $value3 . "<br>";
      foreach ($value3 as $key2 => $value2) {
        //echo $key2 . " - " . $value2 . "<br>";
        foreach ($value2 as $key1 => $value1) {
          //echo $key1 . " - " . $value1 . "<br>";
          foreach ($value1 as $key0 => $value0) {
            if($key2 == "subContentId"){
              $sorted_questions[] = array(
                $value2['subContentId'] => $value1['question'],
                "language" => $value5['language'],
              );
              continue 3;
            }
            //echo $key0 . " - " . $value0 . "<br>";
              
          }
        }
      }
    }
  }
}
var_dump($sorted_questions);

//ORDER WRITE OUT
//var_dump($order_type_questions);
foreach($order_type_questions as $key3 => $value3){
  //echo $key . " - " . $value . "<br>";
  foreach ($value3 as $key => $value) {
    if($key == "taskDescription"){
      //echo $key . " - " . $value . "<br>";
    }   
  }
}

//CLASSIFICATION WRITE OUT
//var_dump($drag_and_drop_type_questions);
foreach($drag_and_drop_type_questions as $key3 => $value3){
  //echo $key . " - " . $value . "<br>";
  
}

//Filling up with all the individual questions
foreach ($conn->query($sql_additionals) as $row) {
  $addition = json_decode($row['additionals'],true);
  $additionals[] = $addition;
  $sub = $addition['extensions']['http://h5p.org/x-api/h5p-subContentId'] ?? "";
  $id = $row['content_id'] . (!empty($sub) ? "-" . $sub : "");
  if(empty($sub)){
    $descriptions[$row['content_id']] = $row['description'];
  }else{
    $descriptions[$sub] = $row['description'];
  }
  $answers[$row['user_id']][$id] = $row['response'];
}



foreach ($data as &$usr) {
  foreach ($descriptions as $i => $description) {
    $found = false;
    foreach ($usr as $j => $user_answers) {
      if($user_answers['description'] == $description['description']){
      $found = true;
      break;
      }
    }   
    if($found == false){
      $usr[] = array(
        "description" => $description['description'],
        "response" => "",
      );
    }   
  }
}

?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>Hello, world!</title>
  </head>
  <body>
    
  <table class="table">
      <thead>
        <tr>
          <th>#</th>
          <?php
            //Writing out all distinct questions  
            foreach ($sorted_questions as $key => $value) {
              if($value['language'] == "en"){
                foreach ($value as $key1 => $value1) {
                  if($key1 != "language")
                  echo "<th>" . "<pre>". $value1 ."</pre>" . "</th>"; 
                }
              }   
            }
            //echo "<th>" . "<pre>". $value ."</pre>" . "</th>";     
          ?>
        </tr>
      </thead>
      <tbody>
        <?php 
          //Writes out users at the start of the line, then their responses
          foreach ($answers as $user => $responses) {
            echo "<tr>";
            echo "<td>".$user."</td>";        
            foreach ($descriptions as $id => $description) {
              echo "<td>".($responses[$id] ?? "")."</td>";                 
            }
            echo "</tr>";
          }
        ?>
      </tbody>
    </table>
    </body>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</html>




















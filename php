<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

include_once '../../config/Database.php';
include_once '../../models/Category.php';
// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate blog post object
$category = new Category($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

$category->name = $data->name;

// Create Category
if($category->create()) {
    echo json_encode(
        array('message' => 'Category Created')
    );
} else {
    echo json_encode(
        array('message' => 'Category Not Created')
    );
}
32 api/category/delete.php
@@ -0,0 +1,32 @@
<?php
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: DELETE');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Category.php';
  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog post object
  $category = new Category($db);

  // Get raw posted data
  $data = json_decode(file_get_contents("php://input"));

  // Set ID to UPDATE
  $category->id = $data->id;

  // Delete post
  if($category->delete()) {
      echo json_encode(
          array('message' => 'Category deleted')
      );
  } else {
      echo json_encode(
          array('message' => 'Category not deleted')
      );
  }
28 api/category/read_single.php
@@ -0,0 +1,28 @@
<?php

  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/Category.php';
  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();
  // Instantiate blog category object
  $category = new Category($db);

  // Get ID
  $category->id = isset($_GET['id']) ? $_GET['id'] : die();

  // Get post
  $category->read_single();

  // Create array
  $category_arr = array(
      'id' => $category->id,
      'name' => $category->name
  );

  // Make JSON
  print_r(json_encode($category_arr));
34 api/category/update.php
@@ -0,0 +1,34 @@
<?php
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: PUT');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Category.php';
  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog post object
  $category = new Category($db);

  // Get raw posted data
  $data = json_decode(file_get_contents("php://input"));

  // Set ID to UPDATE
  $category->id = $data->id;

  $category->name = $data->name;

  // Update post
  if($category->update()) {
      echo json_encode(
          array('message' => 'Category Updated')
      );
  } else {
      echo json_encode(
          array('message' => 'Category not updated')
      );
  }
116 models/Category.php
@@ -17,7 +17,7 @@ public function __construct($db) {
    // Get categories
    public function read() {
        // Create query
        $query = 'SELECT 
      $query = 'SELECT
        id,
        name,
        created_at
@@ -34,4 +34,116 @@ public function read() {

            return $stmt;
        }
  }

    // Get Single Category
    public function read_single(){
        // Create query
        $query = 'SELECT
          id,
          name
        FROM
          ' . $this->table . '
      WHERE id = ?
      LIMIT 0,1';

        //Prepare statement
        $stmt = $this->conn->prepare($query);

        // Bind ID
        $stmt->bindParam(1, $this->id);

        // Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // set properties
        $this->id = $row['id'];
        $this->name = $row['name'];
    }

    // Create Category
    public function create() {
        // Create Query
        $query = 'INSERT INTO ' .
            $this->table . '
    SET
      name = :name';

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->name = htmlspecialchars(strip_tags($this->name));

        // Bind data
        $stmt-> bindParam(':name', $this->name);

        // Execute query
        if($stmt->execute()) {
            return true;
        }

        // Print error if something goes wrong
        printf("Error: $s.\n", $stmt->error);

        return false;
    }

    // Update Category
    public function update() {
        // Create Query
        $query = 'UPDATE ' .
            $this->table . '
    SET
      name = :name
      WHERE
      id = :id';

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind data
        $stmt-> bindParam(':name', $this->name);
        $stmt-> bindParam(':id', $this->id);

        // Execute query
        if($stmt->execute()) {
            return true;
        }

        // Print error if something goes wrong
        printf("Error: $s.\n", $stmt->error);

        return false;
    }

    // Delete Category
    public function delete() {
        // Create query
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        // clean data
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Bind Data
        $stmt-> bindParam(':id', $this->id);

        // Execute query
        if($stmt->execute()) {
            return true;
        }

        // Print error if something goes wrong
        printf("Error: $s.\n", $stmt->error);

        return false;
    }
}

<?php
  const DSN = "mysql:host=localhost;port=8889;dbname=projectNew;charset=utf8mb4" ;
  const USER = "root" ;
  const PASSWORD = "root" ; 

  // connect to database, $db represents mysql dbms
  try {
   $db = new PDO(DSN, USER, PASSWORD) ; 
   $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION) ;
 } catch( PDOException $ex) {
     echo "<p>Connection Error:</p>" ;
     echo "<p>", $ex->getMessage(), "</p>" ;
     exit ;
 }

  function checkUser($email, $rawPass) {
   global $db ;
   $stmt = $db->prepare("SELECT * FROM users WHERE email = ?") ; 
   $stmt->execute([$email]) ;
   if ($stmt->rowCount()) {
      // email exists
      $user = $stmt->fetch() ;
      return password_verify($rawPass, $user["password"]) ;
   }
   return false ; 
}

function seeFriendList($id) {
   $sql = "SELECT u.name, u.surname, f.user_id, f.friend_id
        FROM users u
        JOIN friends f ON u.id = f.user_id or f.friend_id=u.id
        WHERE (f.friend_id = $id or f.user_id=$id) and u.id != $id" ; 

   global $db;

   $result = $db->query($sql);
   if ($result->rowCount() > 0) {
          echo "<ul>";
       while ($row = $result->fetch()) {
          echo "<li>";
          echo "<div class='invisible'>".$row['user_id']."</div>"; //kim
          echo "<div class='invisible'>".$row['friend_id']."</div>"; //kime
          echo "" . $row["name"] . " " . $row["surname"] ."     <i class='fa-solid fa-trash'></i>"."</li><br>";
       }
   } else {
       echo "No friends.";
   }
          echo "</ul>";
} 

function searchFriend($friend,$id){
  global $db;
   $stmt = $db->prepare("SELECT * FROM users where (name=? or surname=? or email=?) and id != ? ");
   $stmt->execute([$friend,$friend,$friend,$id]);

  return $stmt->fetchAll();
}

function getUser($email) {
   global $db ;
   $stmt = $db->prepare("SELECT * FROM users WHERE email = ?") ; 
   $stmt->execute([$email]) ;
   return $stmt->fetch() ; 
}

  function validSession() {
   return isset($_SESSION["user"]) ;
}

function getNotifications($toUserId) {
  global $db;
  $stmt = $db->prepare("SELECT * FROM notifications WHERE to_user_id = ?");
  $stmt->execute([$toUserId]);
  return $stmt->fetchAll();
}
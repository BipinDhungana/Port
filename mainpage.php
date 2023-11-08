<?php
@include 'config.php';

if (isset($_POST['add']) || isset($_POST['sub'])) {
  $number = $_POST['number'];
  $number = filter_var($number, FILTER_SANITIZE_STRING);

  $new_food = $_POST['new_food'];
  $new_food = filter_var($new_food, FILTER_SANITIZE_STRING);

  $new_date = $_POST['new_date'];
  $new_date = filter_var($new_date, FILTER_SANITIZE_STRING);

  $student_id = $_POST['student_id'];
  $fetch_query = $conn->prepare("SELECT amount FROM `student` WHERE stu_id = ?");
  $fetch_query->execute([$student_id]);
  $existing_amount = $fetch_query->fetchColumn();

  $fetch_food = $conn->prepare("SELECT food FROM `student` where stu_id= ? ");
  $fetch_food->execute([$student_id]);
  $existing_food = $fetch_food->fetchColumn();

  $fetch_date = $conn->prepare("SELECT date FROM `student` where stu_id=?");
  $fetch_date->execute([$student_id]);
  $existing_date = $fetch_date->fetchcolumn();

  $last_updated = $conn->prepare("SELECT last_updated FROM `student` WHERE stu_id=?;");
  $last_updated->execute([$student_id]);
  $exis_date = $last_updated->fetchColumn();



  function getNepaliDate($english_date)
  {
    $ndate = NepaliCalender::getInstance()->eng_to_nep($english_date);
    $ndate = $ndate['nmonth_in_nepali'] . ' ' . $ndate['date_in_nepali'] . ', ' . ($ndate['year']);
    return $ndate;
  }

  include('NepaliCalender.php');
  //AD to BS Conversion
  $english_date = $exis_date;
  $nepali_date = getNepaliDate($english_date);
  echo $nepali_date;





  if (isset($_POST['add'])) {
    $total_amount = $existing_amount + $number;
    $newdate = $existing_date . ", "  . $new_date;
    $new_food = $existing_food . ", " . $new_food . " (" . $nepali_date . ")";
  } elseif (isset($_POST['sub'])) {
    $total_amount = $existing_amount - $number;
  }

  $update_query = $conn->prepare("UPDATE student SET amount = ? WHERE stu_id = ?");
  $update_query->execute([$total_amount, $student_id]);

  $update_food = $conn->prepare("UPDATE student SET food=? where stu_id=?");
  $update_food->execute([$new_food, $student_id]);



  session_start();

  if (!isset($_SESSION['form_submitted'])) {
    header("Location: mainpage.php");
    exit();
  }
}

if (isset($_POST['sbutton'])) {
  $search_query = $_POST['search'];
  $sql = "SELECT * FROM `student` WHERE name = '$search_query'";
  $result = $conn->query($sql);

  if ($result->rowCount() > 0) {
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
      foreach ($row as $key => $value) {
        echo $key . ": " . $value . "<br>";
      }
      echo "<br>";
    }
  } else {
    echo "No results found.";
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Home page</title>

  <!--connect -->
  <link rel="stylesheet" href="style.css" />

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

</head>

<body>
  <h3 class="top">Students here</h3>
  <form action="" method="POST" class="box">
    <input type="text" placeholder="search" class="in" name="search">
    <input type="submit" class="su" value="Search" name="sbutton">
    <a href="Register.php" class="anchor">Register Page</a>
  </form>

  <section class="container-registera">
    <h1>Students</h1>
    <?php
    $total_price = 0;
    $select = $conn->prepare("SELECT * FROM `student`");
    $select->execute();
    if ($select->rowCount() > 0) {

      while ($fetch_student = $select->fetch(PDO::FETCH_ASSOC)) {
    ?>
        <div class="box">
          <p> Name : <span><?= $fetch_student['name']; ?></span> </p>
          <p> Class : <span><?= $fetch_student['class']; ?></span> </p>
          <p> Address : <span><?= $fetch_student['address']; ?></span> </p>
          <p> Phone : <span><?= $fetch_student['phone']; ?></span> </p>
          <p> Amount: <span><?= $fetch_student['amount']; ?></span> </p>
          <p> Food : <span><?= $fetch_student['food']; ?></span> </p>
          <p> Registered on : <span><?= $fetch_student['date']; ?></span> </p>
          <form action="mainpage.php" method="POST" enctype="multi/form-data" class="box">
            <input type="number" placeholder="Add or Sub" class="in" name="number" required>
            <input type="text" placeholder="Food" class="in" name="new_food" required>
            <input type="date" placeholder="Date" class="in" name="new_date" required>
            <input type="hidden" name="student_id" value="<?= $fetch_student['stu_id']; ?>">
            <input type="submit" value="Add" class="su" name="add">
            <input type="submit" value="sub" class="su" name="sub">
            <p> Total amount : <span>Rs: <?= $fetch_student['amount']; ?></span></p>

          </form>

        </div>
    <?php

      }
    } else {
      echo '<p class="empty">no students registered yet!</p>';
    }
    ?>

  </section>
</body>

</html>
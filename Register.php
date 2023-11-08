<?php

@include 'config.php';
if (isset($_POST['submit'])) {
  $name = $_POST['name'];
  $name = filter_var($name, FILTER_SANITIZE_STRING);
  $class = $_POST['class'];
  $class = filter_var($class, FILTER_SANITIZE_STRING);
  $address = $_POST['address'];
  $address = filter_var($address, FILTER_SANITIZE_STRING);
  $phone = $_POST['phone'];
  $phone = filter_var($phone, FILTER_SANITIZE_STRING);
  $amount = $_POST['amount'];
  $amount = filter_var($amount, FILTER_SANITIZE_STRING);
  $food = $_POST['food'];
  $food = filter_var($food, FILTER_SANITIZE_STRING);


  $select = $conn->prepare("SELECT * FROM `student` WHERE phone=?");
  $select->execute([$phone]);

  if ($select->rowcount() > 0) {
  } else {
    $insert = $conn->prepare("INSERT INTO `student` (name,class,address,phone,amount,food) VALUES(?,?,?,?,?,?)");
    $insert->execute([$name, $class, $address, $phone, $amount, $food]);

    $message[] = 'Registered successfully';
  }
}
?>
<?php

if (isset($message)) {
  foreach ($message as $message) {
    echo '
<div class="message">
  <span>' . $message . '</span>
  <i  onclick="this.parentElement.remove();"></i>
</div>
';
  }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register page</title>

  <!--connect -->
  <link rel="stylesheet" href="style.css" />

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">


</head>

<body>
  <h3 class="top">Register here</h3>
  <section class="container-register">
    <h1 class="student">Students</h1>
    <form action="" enctype="multi/form-data" method="POST">
      <input class="in" type="text" name="name" placeholder="Enter the name." required />
      <input class="in" type="text" name="class" placeholder="Enter the class." />
      <input class="in" type="text" name="address" placeholder="Enter the address." required />
      <input class="in" type="number" name="phone" placeholder="Phone number." required />
      <input class="in" type="number" name="amount" placeholder="Amount to be added." required />
      <input class="in" type="text" name="food" placeholder="Food" required />
      <input class="su" type="submit" value="Register Now" name="submit" />
      <a class="anchor" href="mainpage.php">Go to Home</a>
    </form>
  </section>
</body>

</html>
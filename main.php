<?php

// Połączenie z bazą danych
$db = new mysqli('localhost', 'user', 'password', 'database');

// Sprawdzenie, czy formularz logowania został wysłany
if (isset($_POST['login-submit'])) {

  // Pobranie danych z formularza
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Sprawdzenie, czy login i hasło są prawidłowe
  $query = "SELECT * FROM users WHERE username='$username' AND password='$password' AND active=1";
  $result = $db->query($query);
  if ($result->num_rows > 0) {
    // Zalogowanie użytkownika i przekierowanie do panelu użytkownika
    session_start();
    $_SESSION['username'] = $username;
    header('Location: user-panel.php');
  } else {
    // Wyświetlenie komunikatu o błędzie
    $error = "Nieprawidłowy login lub hasło lub konto jest nieaktywne";
  }

}

// Sprawdzenie, czy formularz rejestracji został wysłany
if (isset($_POST['register-submit'])) {

  // Pobranie danych z formularza
  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = $_POST['password'];

  // Sprawdzenie, czy login jest już zajęty
  $query = "SELECT * FROM users WHERE username='$username'";
  $result = $db->query($query);
  if ($result->num_rows > 0) {
    // Wyświetlenie komunikatu o błędzie
    $error = "Login jest już zajęty";
  } else {
    // Dodanie użytkownika do bazy danych
    $token = uniqid();
    $query = "INSERT INTO users (username, email, password, token, active) VALUES ('$username', '$email', '$password', '$token', 0)";
    $result = $db->query($query);

    // Wyślij e-mail z linkiem aktywacyjnym do użytkownika
    $to = $email;
    $subject = "Aktywacja konta";
    $message = "Kliknij w link, aby aktywować swoje konto: http://example.com/activate.php?token=$token";
    mail($to, $subject, $message);
    // Sprawdzenie, czy formularz aktywacji został wysłany
if (isset($_GET['token'])) {
    // Pobranie tokenu z adresu URL
    $token = $_GET['token'];
  
    // Aktywacja konta użytkownika
    $query = "UPDATE users SET active=1 WHERE token='$token'";
    $result = $db->query($query);
  
    // Przekierowanie do panelu logowania
    header('Location: login.php');
  }
  
  // Sprawdzenie, czy użytkownik jest już zalogowany
  if (isset($_SESSION['username'])) {
    // Wyświetlenie panelu użytkownika
    echo "Witaj, {$_SESSION['username']}";
    echo '<br><a href="logout.php">Wyloguj się</a>';
  } else {
    // Wyświetlenie formularza logowania
    echo '
    <form action="login.php" method="post">
      <input type="text" name="username" placeholder="Nazwa użytkownika">
      <input type="password" name="password" placeholder=" Hasło">
      <input type="submit" name="login-submit" value="Zaloguj się">
    </form>
    ';
  
    // Wyświetlenie formularza rejestracji
    echo '
    <form action="login.php" method="post">
      <input type="text" name="username" placeholder=" Nazwa użytkownika">
      <input type="email" name="email" placeholder=" Adres e-mail">
      <input type="password" name="password" placeholder=" Hasło">
      <input type="submit" name="register-submit" value="Zarejestruj się">
    </form>
    ';
  
    // Wyświetlenie ewentualnego komunikatu o błędzie
    if (isset($error)) {
      echo $error;
    }
  }
  
  ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pet Adoption Center</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; background-color: #f4f4f9; }
        .login-box { background: white; padding: 20px; border-radius: 10px; width: 300px; margin: 0 auto; box-shadow: 0px 0px 10px gray; }
        input, button { margin: 10px 0; padding: 10px; width: 90%; }
        button { background-color: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background-color: #0056b3; }
    </style>
</head>
<body>

<h1>Welcome to the Pet Adoption Center!</h1>
<p>Where you can find your furrrever friend!</p>

<div class="login-box">
    <h2>Log In</h2>
    <form action="login_process.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Log In</button>
    </form>
</div>

</body>
</html>
<?php
require "./db.php";
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_GET["action"])) {

    $action = $_GET["action"];
    switch ($action) {
        case 'signup':

            $user_email = $_GET["email"];
            $user_nick = $_GET["nickname"];
            $user_password = $_GET["password"];

            $answ = $mysql->query("SELECT * FROM `users` WHERE `email` = '$user_email'");

            if (mysqli_num_rows($answ)) {
                echo json_encode(["success" => false]);
            } else {

                $userIP = getenv('REMOTE_ADDR');
                $custom_token = md5($user_password);

                $mysql->query("INSERT INTO `users` (`nickname`,`email`,`password`,`ip_address`,`address`,`balance`, `profile_img`) VALUES ('$user_nick','$user_email','$user_password','$userIP','sdfsd', 0,'https://www.011global.com/Account/Slices/user-anonymous.png')");
                $last_id = mysqli_insert_id($mysql);
                $mysql->query("INSERT INTO `auth` (`hash`,`user_id`) VALUES ('$custom_token', $last_id)");
                echo json_encode(["success" => true, "token" => $custom_token]);
            }

            break;
        case 'signin':
            $user_email = $_GET["email"];
            $user_password = $_GET["password"];

            $data = $mysql->query("SELECT * FROM `users` WHERE `email` = '$user_email' AND `password` = '$user_password'");

            if ($data->num_rows) {
                $row = $data->fetch_assoc();
                $user_id = $row["id"];
                $custom_token = md5($user_password);
                $mysql->query("INSERT INTO `auth` (`hash`,`user_id`) VALUES ('$custom_token','$user_id')");

                echo json_encode(["success" => true, "user_data" => $row, "token" => $custom_token]);
            } else {
                echo json_encode(["success" => false]);
            }

            break;
        case 'get_user_data':
            $token = $_GET["token"];

            $search_token = $mysql->query("SELECT * FROM `auth` WHERE `hash`= '$token'");
            if ($search_token->num_rows) {
                $user_id = $search_token->fetch_assoc()["user_id"];
                $user_data = $mysql->query("SELECT * FROM `users` WHERE `id`='$user_id'");
                $row = $user_data->fetch_assoc();
                echo json_encode(["success" => true, "user_data" => $row]);
            } else {
                echo json_encode(["success" => false]);
            }
            break;
        case 'log_out':
            $token = $_GET["token"];

            $mysql->query("DELETE FROM `auth` WHERE `hash` = '$token'");
            echo json_encode(["success" => true]);
            break;
        case 'get_hacked':
            $user_id = $_GET["user_id"];
            $hacked = $mysql->query("SELECT * FROM `hacked` WHERE `user_id` = '$user_id'");
            $row = $hacked->fetch_all(PDO::FETCH_ASSOC);
            echo json_encode(["hacked" => $row]);
            break;
        case 'get_blogs':
            $select_blogs = $mysql->query("SELECT * FROM `blogs`");
            $row = $select_blogs->fetch_all(PDO::FETCH_ASSOC);
            echo json_encode(["blogs" => $row]);
            break;
        case 'update_data':
            $new_nickname = $_GET['nickname'];
            $new_profile_img = $_GET['profile_img'];
            $user_id = $_GET['user_id'];
            $mysql->query("UPDATE `users` SET `nickname` = '$new_nickname', `profile_img` = '$new_profile_img' WHERE `id` = '$user_id'");
            echo json_encode(["success" => true]);
            break;
        case 'hack_account':
            $user_id = $_GET['user_id'];
            $account_url = $_GET['url'];
            $mysql->query("INSERT INTO `hacked` (`account_url`,`user_id`) VALUES ('$account_url','$user_id')");
            echo json_encode(["success" => true]);
            break;
        case 'new_order':
            $token = '5b228c526d13002f3e001330243f9b2b';
            $order_id = time() . mt_rand(); // Генерация уникального идентификатора заказа на основе метки времени и случайного числа
            $amount = $_GET['amount']; // Установка суммы для оплаты



            $data = [
                'shop_id' => 848, // ID вашего магазина
                'token' => $token,
                'order_id' => $order_id,
                'amount' => $amount,
                'data' => json_encode($params)
            ];

            $ch = curl_init('https://lk.rukassa.is/api/v1/create'); // Инициализация сеанса cURL
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);

            $result = json_decode(curl_exec($ch)); // Выполнение запроса cURL и декодирование JSON-ответа
            curl_close($ch);

            echo json_encode(["success" => true, "location" => $result->url]);
            break;
        case 'auth_google':
            $user_email = $_GET['email'];
            $google_id = $_GET['google_id'];
            $name = $_GET['name'];
            $profile_img = $_GET['image'];
            $token = $_GET['token'];
            $auth = $mysql->query("SELECT * FROM `users` WHERE `google_id`='$google_id'");
            if ($auth->num_rows) {
                $row = $auth->fetch_assoc();
                $user_id = $row["id"];
                $mysql->query("INSERT INTO `auth` (`hash`,`user_id`) VALUES ('$token', $user_id)");
                echo json_encode(["success" => true, "user_data" => $row, "token" => $token]);
            } else {
                $userIP = getenv('REMOTE_ADDR');
                $mysql->query("INSERT INTO `users` (`nickname`,`email`,`ip_address`,`address`,`balance`,`profile_img`,`google_id`) VALUES ('$name','$user_email','$userIP',null,0,'$profile_img','$google_id')");
                $last_id = mysqli_insert_id($mysql);
                $mysql->query("INSERT INTO `auth` (`hash`,`user_id`) VALUES ('$token', $last_id)");
                echo json_encode(["success" => true, "google_id" => $google_id,"token" => $token]);
            }
    }
}

$mysql->close();    
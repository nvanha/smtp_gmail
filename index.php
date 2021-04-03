<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gmail SMTP</title>
    <link
        rel="stylesheet"
        href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"
    />
    <link 
		href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" 
		rel="stylesheet"
	/>
    <link rel="stylesheet" href="style/reset.css" />
    <link rel="stylesheet" href="style/style.css" />
</head>
<body>
<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    include 'function.php';
    if (isset($_GET['action']) && $_GET['action'] == 'send') {
        if (empty($_POST['email'])) {
            $error = "You must enter your email address";
        } elseif (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $error = "Incorrect email format";
        } elseif (empty($_POST['content'])) {
            $error = "You must enter text message";
        }

        if (isset($_FILES['file_upload'])) {
            $uploadedFile = $_FILES['file_upload'];
            $result = uploadFiles($uploadedFile);
            if (!empty($result['errors'])) {
                $error = $result['errors'];
            } else {
                $uploadedFile = $result['uploaded_files'];
            }
        }

        if (!isset($error)) {
            include 'library.php';
            require 'vendor/autoload.php';
            $mail = new PHPMailer(true);
            try {
                $mail->CharSet = "UTF-8";
                $mail->SMTPDebug = 0;
                $mail->isSMTP();
                $mail->Host = SMTP_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = SMTP_UNAME;
                $mail->Password = SMTP_PWORD;
                $mail->SMTPSecure = 'ssl';
                $mail->Port = SMTP_PORT;
                $mail->setFrom(SMTP_UNAME, "Send Person");
                $mail->addAddress($_POST['email'], "Received Person");
                $mail->addReplyTo(SMTP_UNAME, "Information");
                // $mail->addCC('cc@example.com');
                // $mail->addBCC('bcc@example.com');
                if (!empty($uploadedFile)) {
                    foreach ($uploadedFile as $file) {
                        $mail->addAttachment(realpath($file));
                    }
                }
                $mail->isHTML(true);
                $mail->Subject = $_POST['title'];
                $mail->Body = $_POST['content'];
                $mail->AltBody = $_POST['content'];
                $result = $mail->send();
                if (!$result) {
                    $error = "An error occurred while sending mail";
                }
            } catch (Exception $e) {
                echo "Message could not be send, Mailer Error: ", $mail->ErrorInfo;
            }
        }
?>
<div class="wrapper">
    <div class="container">
        <div class="box-content">
            <div class="error">
                <?php 
                    if(isset($error)) {
                        echo "<p>" . $error . "</p>"; 
                    } else {
                        echo "<p>Email successfully sent</p>";  
                    }
                ?>
                <a href="index.php">Back to mailing form</a>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="container">
        <div class="box-content">
            <form action="?action=send" method="post" id="form" enctype="multipart/form-data">
                <div class="form-group">
                    <i class="fa fa-envelope"></i>
                    <input required type="text" name="email" placeholder="example@gmail.com" class="form-control" />
                </div>
                <div class="form-group">
                    <i class="fa fa-paperclip"></i>
                    <input required multiple type="file" name="file_upload[]" class="form-control" />
                </div>
                <div class="form-group">
                    <i class="fa fa-font"></i>
                    <input required type="text" name="title" placeholder="Title" class="form-control" />
                </div>                
                <div class="form-group">
                    <i class="fa fa-comments"></i>
                    <textarea required name="content" placeholder="Text message" class="form-control"></textarea>
                </div>
                <input type="submit" value="Send Email" />
            </form>
        </div>
    </div>
</div>
<?php } ?>
</body>
</html>
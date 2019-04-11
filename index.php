<?php 
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    require 'vendor/autoload.php';
    require 'vendor/phpmailer/phpmailer/src/Exception.php';
    require 'vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require 'vendor/phpmailer/phpmailer/src/SMTP.php';

    if (array_key_exists('to', $_POST)) {
    $err = false;
    $msg = '';
    $email = '';
    
    //Apply some basic validation and filtering to the query
    if (array_key_exists('reason', $_POST)) {
        //Limit length and strip HTML tags
        $reason = substr(strip_tags($_POST['reason']), 0, 16384);
    } else {
        $reason = '';
        $msg = 'No feedback provided!';
        $err = true;
    }
    //Apply some basic validation and filtering to the name
    if (array_key_exists('username', $_POST)) {
        //Limit length and strip HTML tags
        $username = substr(strip_tags($_POST['username']), 0, 255);
    } else {
        $username = '';
    }
    //Validate to address
    //Never allow arbitrary input for the 'to' address as it will turn your form into a spam gateway!
    //Substitute appropriate addresses from your own domain, or simply use a single, fixed address
    if (array_key_exists('to', $_POST)) {
        $to =  'suleabimbola@gmail.com';
    } else {
        $to = 'larry56@gmail.com';
    }
    //Make sure the address they provided is valid before trying to use it
    if (array_key_exists('email', $_POST) and PHPMailer::validateAddress($_POST['email'])) {
        $email = $_POST['email'];
    } else {
        $msg .= "Error: invalid email address provided";
        $err = true;
    }

    if (array_key_exists('attachment', $_FILES)) {
        $img_name = $_FILES['attachment']['name'];
        $upload = tempnam(sys_get_temp_dir(), hash('sha256', $_FILES['attachment']['name']));
        $uploadfile = $_SERVER['DOCUMENT_ROOT'].'/mail/assets/img/'.$img_name;
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $uploadfile)) {
            if (!$err) {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                // $mail->SMTPDebug = 3;
                $mail->SMTPSecure = 'tls';
                $mail->Host = 'smtp.gmail.com';
                // set a port
                $mail->Port = 587;
                $mail->SMTPAuth = true;
                // set login detail for gmail account
                $mail->Username = 'username';
                $mail->Password = 'password';
                $mail->CharSet = 'utf-8';
                // set subject
                $mail->setFrom($email, $username);
                $mail->addAddress($to);
                $mail->addReplyTo($email, $username);
                $mail->addAttachment($uploadfile, 'My uploaded image');
                $mail->IsHTML(true);
                $mail->Subject = 'Contact form: Testing Feedback';
                $mail->Body = $reason;
                if (!$mail->send()) {
                    $msg .= "Mailer Error: " . $mail->ErrorInfo;
                } else {
                    $msg .= "Message sent!";
                }
            }
        } else {
                    $msg .= 'Failed to move file to ' . $uploadfile;
        }
    }
}   
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Testing Feedback</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>
<body>
    
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary mt-3 ml-3" data-toggle="modal" data-target="#exampleModal">
        Testing Feedback
    </button>
    
      <!-- Modal -->
    <?php if(empty($msg)){ ?>
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data" class="container" id="needs-validation" novalidate>
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Feedback Form</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="username" placeholder="username">
                                    <div class="invalid-feedback">
                                        Please provide username.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="email" class="form-control" name="email" placeholder="email">
                                    <div class="invalid-feedback">
                                        Please provide valid email.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <select name="to" class="form-control">
                                        <option value="sales">Sales</option>
                                        <option value="support" selected="selected">Support</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <textarea class="form-control" name="reason" rows="2" required=""></textarea>
                                    <div class="invalid-feedback">
                                        Please provide feedback information.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="file" name="attachment">
                                    <div class="invalid-feedback">
                                        Please attach file 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="feedback">Send Feedback</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php }else{ 
        echo $msg;
    } ?>
    
        
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script>
        (function() {
            'use strict';
            window.addEventListener('load', function() {
            var form = document.getElementById('needs-validation');
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    
                event.preventDefault();
                event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
            }, false);
        })();
    </script>
</body>
</html> 
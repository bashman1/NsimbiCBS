<?php
 function sendEmail($mail_to, $mail_subject, $mail_body)
{

    $cURL_key = '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';
    $mail_from = 'ucscucbsdev@gmail.com';

    $curl = curl_init();


    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.sendgrid.com/v3/mail/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{\"personalizations\": [{\"to\": [{\"email\": \"$mail_to\"}]}],\"from\": {\"email\": \"$mail_from\"},\"subject\": \"$mail_subject\",\"content\": [{\"type\": \"text/html\", \"value\": \"$mail_body\"}]}",
        CURLOPT_HTTPHEADER => array(
            "authorization: Bearer $cURL_key",
            "cache-control: no-cache",
            "content-type: application/json"
        ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        echo $response;
    }
}

$url = 'https://staging.ucscucbs.net/admin/staff/new';
$mail_subject ='Sign up with UCSCU CBS';


sendEmail('academicsyasira@gmail.com',$mail_subject, 
"<style>p{font-size: 14px;}</style><img src='https://app.ucscucbs.net/client/images/unnamed.png' style='margin: auto;'/><p>Hello,</p><p>You've just had a UCSCU CBS account created for you.</p><p>You must now complete signing up by setting up a password for your account using link below: <br/><br/><button style='background-color: #4CAF50;border: none;color: white;padding: 15px 32px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;'>Set Password</button><br/></p><p>If this email was sent by mistake, please ignore this email and contact the administrator at your institution. If you need further support with siging up, please contact our team at <a href='mailto:support@ucscucbs.net'>support@ucscucbs.net</a>.</p><p>Thank you for using UCSCU CBS!</p><p>-The UCSCU CBS Team</p>"
);
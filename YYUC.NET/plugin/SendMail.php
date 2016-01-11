<?php
class SendMail{
	/**
	 * 
	 * @param string $key 配置key
	 * @param string $to 收件人
	 * @param string $subject 主题
	 * @param string $body 内容
	 */
	public static function normal_send($key, $to, $subject, $body ,$toname = ''){
		$pzarr = Conf::$email[$key];
		if($pzarr['protocol'] == 'smtp'){
			$mail = new Mailer();
			$mail->IsSMTP();
			$mail->Host       = $pzarr['smtp_host']; // SMTP server
			$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
			$mail->SMTPAuth   = true;                  // enable SMTP authentication
			$mail->Port       = isset($pzarr['smtp_port']) ? $pzarr['smtp_port'] : 25;                    // set the SMTP port for the GMAIL server
			$mail->Username   = $pzarr['smtp_user']; // SMTP account username
			$mail->Password   = $pzarr['smtp_pass'];        // SMTP account password
			$mail->AddReplyTo($pzarr['from'][0], $pzarr['from'][1]);
			$mail->SetFrom($pzarr['from'][0], $pzarr['from'][1]);
			$mail->AddAddress($to, $toname);
			$mail->Subject = $subject;
			$mail->Body = $body;
			$mail->IsHTML();
			$mail->Send();
		}
	}
}
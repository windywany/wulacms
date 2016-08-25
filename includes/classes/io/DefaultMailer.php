<?php
/**
 *
 * @author Leo
 *        
 */
class DefaultMailer implements IMailer {
	/**
	 * 邮件发送器
	 *
	 * @var PHPMailer
	 */
	private $phpmailer;
	public function __construct() {
		$type = cfg ( 'smtp_type@smtp' );
		if ($type) {
			$this->phpmailer = new PHPMailer ( true );
			$this->config ( $type );
		}
	}
	/*
	 * (non-PHPdoc) @see IMailer::send()
	 */
	public function send($to, $subject, $message, $attachments = array()) {
		if (! $this->phpmailer) {
			return false;
		}
		try {
			$this->phpmailer->ClearAddresses ();
			$this->phpmailer->ClearAllRecipients ();
			$this->phpmailer->ClearAttachments ();
			$this->phpmailer->ClearBCCs ();
			$this->phpmailer->ClearCCs ();
			$this->phpmailer->ClearCustomHeaders ();
			
			$this->phpmailer->Subject = $subject;
			if ($this->phpmailer->ContentType == 'text/html') {
				$this->phpmailer->MsgHTML ( $message, WEB_ROOT );
			} else {
				$this->phpmailer->Body = $message;
				$this->phpmailer->WordWrap = 66;
			}
			if (! is_array ( $to )) {
				$_to = explode ( '@', $to );
				$to = array ($to,$_to [0] );
			}
			$this->phpmailer->AddAddress ( $to [0], $to [1] );
			if (! empty ( $attachments )) {
				$attachments = is_array ( $attachments ) ? $attachments : array ($attachments );
				foreach ( $attachments as $i => $att ) {
					$this->phpmailer->AddAttachment ( $att, 'attach-' . $i );
				}
			}
			return $this->phpmailer->Send ();
		} catch ( phpmailerException $e ) {
			log_warn ( "邮件发送失败:" . $e->getMessage () );
			return false;
		} catch ( Exception $e ) {
			log_warn ( "邮件发送失败:" . $e->getMessage () );
			return false;
		}
	}
	
	/*
	 * (non-PHPdoc) @see IMailer::setMessageType()
	 */
	public function setMessageType($type) {
		if (! $this->phpmailer) {
			return;
		}
		if ($type == 'html') {
			$this->phpmailer->IsHTML ( true );
		} else {
			$this->phpmailer->IsHTML ( false );
		}
	}
	/**
	 * 配置phpmailer.
	 */
	private function config($type) {
		switch ($type) {
			case 'mail' :
				$this->phpmailer->IsMail ();
				break;
			case 'sendmail' :
				$this->phpmailer->IsSendmail ();
				break;
			case 'qmail' :
				$this->phpmailer->IsQmail ();
				break;
			case 'smtp' :
			default :
				$this->phpmailer->IsSMTP ();
				if (bcfg ( 'smtp_auth@smtp' )) {
					$this->phpmailer->SMTPAuth = true;
					$this->phpmailer->Username = cfg ( 'smtp_user@smtp', 'root@localhost' );
					$this->phpmailer->Password = cfg ( 'smtp_passwd@smtp', 'root' );
				}
				$this->phpmailer->Host = cfg ( 'smtp_host@smtp', 'localhost' );
				$this->phpmailer->Port = icfg ( 'smtp_port@smtp', 25 );
				$this->phpmailer->SMTPSecure = cfg ( 'smtp_secure@smtp', '' );
				break;
		}
		$this->phpmailer->CharSet = 'UTF-8';
		$this->phpmailer->Encoding = 'base64';
		$this->phpmailer->SetFrom ( cfg ( 'smtp_user@smtp', 'root@localhost' ), cfg ( 'smtp_from@smtp', 'KissGO! CMS.' ) );
		$smtp_reply = cfg ( 'smtp_reply@smtp', '' );
		if ($smtp_reply) {
			$this->phpmailer->AddReplyTo ( $this->phpmailer->From, $this->phpmailer->FromName );
		}
		$this->phpmailer->IsHTML ( true );
	}
}
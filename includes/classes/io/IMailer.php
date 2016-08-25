<?php
interface IMailer {
	const HTML = 'html';
	const TXT = 'text';
	/**
	 * 设置邮件正文格式
	 *
	 * @param string $type        	
	 */
	public function setMessageType($type);
	/**
	 * 发送邮件
	 *
	 * @param array $to
	 *        	接收人
	 * @param string $subject
	 *        	主题
	 * @param string $content
	 *        	正文
	 * @param array $attachments
	 *        	附件
	 */
	public function send($to, $subject, $message, $attachments = array());
}
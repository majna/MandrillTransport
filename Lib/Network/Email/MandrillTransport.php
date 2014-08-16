<?php

App::uses('AbstractTransport', 'Network/Email');
App::uses('HttpSocket', 'Network/Http');

/**
 * CakePHP email transport adapter for Mandrill (MailChimp) delivery service
 *
 * It can be used as drop-in replacement for SMTP transport to send transactional emails.
 * Mandrill's templates and merge variables are not supported.
 * https://www.mandrill.com
 *
 * Transactional email is sent using Mandrill's REST API
 * https://mandrillapp.com/api/docs/messages.php.html#method=send
 *
 * Note: If you don't specify mime type for attachments, CakeEmail will set
 * 'application/octet-stream' by default. To specify mime type use:
 *
 *	$email->attachments(array(
 *		'invoice.pdf' => array(
 *			'file' => APP . 'invoices/invoice.pdf',
 *			'mimetype' => 'application/pdf',
 *		)
 *	));
 */
class MandrillTransport extends AbstractTransport {

	/**
	 * Send out email using Mandrill API
	 *
	 * @param CakeEmail $email Email instance
	 * @return array An Array with sent headers and Mandrill json response
	 * @throws SocketException if email could not be sent
	 */
	public function send(CakeEmail $email) {
		$headers = $email->getHeaders(array('subject', 'from', 'to', 'replyTo'));

		$message = array(
			'from_email' => $headers['From'],
			'to' => array(array('email' => $headers['To'])),
			'subject' => mb_decode_mimeheader($headers['Subject']),
			'html' => utf8_encode($email->message('html')),
			'text' => utf8_encode($email->message('text'))
		);

		if (!empty($headers['Reply-To'])) {
			$message['headers']['Reply-To'] = $headers['Reply-To'];
		}

		foreach ($email->attachments() as $name => $file) {
			$message['attachments'][] = array(
				'type' => $file['mimetype'],
				'name' => $name,
				'content' => base64_encode(file_get_contents($file['file']))
			);
		}

		if (!empty($this->_config['message'])) {
			$message = array_merge($message, $this->_config['message']);
		}

		$options = array('header' => array(
				'Accept' => 'application/json',
				'Content-Type' => 'application/json',
			)
		);

		$uri = $this->_config['uri'] . 'messages/send.json';
		$data = array(
			'key' => $this->_config['key'],
			'message' => $message
		);
		$response = $this->_http()->post($uri, json_encode($data), $options);
		$body = utf8_encode($response->body());
		$result = json_decode($body, true);

		if (empty($result['status']) || $result['status'] === 'error') {
			throw new SocketException('Mandrill API error: ' . json_encode($result));
		}

		return array(
			'headers' => $this->_headersToString($headers),
			'message' => json_encode($result)
		);
	}

	/**
	 * Get HTTP client instance
	 *
	 * @return Cake\Network\HttpHttpSocket
	 */
	protected function _http() {
		return new HttpSocket();
	}

}

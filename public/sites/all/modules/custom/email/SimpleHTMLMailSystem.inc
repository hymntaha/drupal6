<?php
/**
 * @file SimpleHTMLMailSystem.inc
 * By ben
 * Date: 1/24/12 11:03 AM
 */
class SimpleHTMLMailSystem implements MailSystemInterface {
  /**
   * Concatenate and wrap the e-mail body for plain-text mails.
   *
   * @param array $message
   *   A message array, as described in hook_mail_alter().
   *
   * @return array The formatted $message.
   */
  public function format(array $message) {
    $message['body'] = implode("\n\n", $message['body']);
    return $message;
  }

  /**
   * Send an e-mail message, using Drupal variables and default settings.
   *
   * @see <a href="http://php.net/manual/en/function.mail.php
  " title="http://php.net/manual/en/function.mail.php
  " rel="nofollow">http://php.net/manual/en/function.mail.php
  </a>   * @see drupal_mail()
   *
   * @param array $message
   *   A message array, as described in hook_mail_alter().
   * @return bool TRUE if the mail was successfully accepted, otherwise FALSE.
   */
  public function mail(array $message) {
    $mimeheaders = array();
    foreach ($message['headers'] as $name => $value) {
      $mimeheaders[] = $name . ': ' . mime_header_encode($value);
    }
    $line_endings = variable_get('mail_line_endings', MAIL_LINE_ENDINGS);
    return mail(
      $message['to'],
      mime_header_encode($message['subject']),
      // Note: e-mail uses CRLF for line-endings. PHP's API requires LF
      // on Unix and CRLF on Windows. Drupal automatically guesses the
      // line-ending format appropriate for your system. If you need to
      // override this, adjust $conf['mail_line_endings'] in settings.php.
      preg_replace('@\r?\n@', $line_endings, $message['body']),
      // For headers, PHP's API suggests that we use CRLF normally,
      // but some MTAs incorrectly replace LF with CRLF. See #234403.
      join("\n", $mimeheaders)
    );
  }
}

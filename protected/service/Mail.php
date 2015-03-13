<?php

/**
 * Class Mail
 */
class Mail
{

    const MAIL_SETTING_TO_CUSTOMER = 'customer';
    const MAIL_SETTING_TO_OP = 'op';

    /**
     * @var
     */
    protected $to;
    /**
     * @var
     */
    protected $cc;
    /**
     * @var
     */
    protected $from;
    /**
     * @var
     */
    protected $sender;
    /**
     * @var
     */
    protected $subject;
    /**
     * @var
     */
    protected $text;
    /**
     * @var
     */
    protected $html;
    /**
     * @var array
     */
    protected $attachments = array();
    /**
     * @var string
     */
    public $protocol = 'mail';
    /**
     * @var
     */
    public $hostname;
    /**
     * @var
     */
    public $username;
    /**
     * @var
     */
    public $password;
    /**
     * @var int
     */
    public $port = 25;
    /**
     * @var int
     */
    public $timeout = 5;
    /**
     * @var string
     */
    public $newline = "\n";
    /**
     * @var string
     */
    public $crlf = "\r\n";
    /**
     * @var bool
     */
    public $verp = false;
    /**
     * @var string
     */
    public $parameter = '';

    /**
     * @param array $from_config
     */
    public function __construct($from_config = array())
    {
        if (isset($from_config) && !empty($from_config)) { //初始化发件箱配置
            $this->protocol = $from_config['protocol'];
            $this->parameter = $from_config['parameter'];
            $this->hostname = $from_config['smtp_host'];
            $this->username = $from_config['smtp_username'];
            $this->password = $from_config['smtp_password'];
            $this->port = $from_config['smtp_port'];
            $this->timeout = $from_config['smtp_timeout'];
            $this->setFrom($from_config['smtp_username']);
            $this->setSender($from_config['sender_name']);
        }
    }

    public static function getInstanceBySetting($mailSetting)
    {
        return new Mail($mailSetting);
    }

    public static function getInstanceForCustomer()
    {
        $mailSetting = HtEmailSetting::model()->findByAttributes(array('setting_name' => 'customer'))->getAttributes();

        return Mail::getInstanceBySetting($mailSetting);
    }

    public static function getInstanceForOP()
    {
        $mailSetting = HtEmailSetting::model()->findByAttributes(array('setting_name' => 'op'))->getAttributes();

        return Mail::getInstanceBySetting($mailSetting);
    }

    public static function getInstanceForSupplier()
    {
        $mailSetting = HtEmailSetting::model()->findByAttributes(array('setting_name' => 'supplier'))->getAttributes();

        return Mail::getInstanceBySetting($mailSetting);
    }

    /**
     * @param $to
     */
    public function setTo($to)
    {
        $this->to = $this->tryFixEmailAddress($to);
    }

    /**
     * @param $cc
     */
    public function setCc($cc)
    {
        $this->cc = $this->tryFixEmailAddress($cc);
    }

    /**
     * @param $from
     */
    public function setFrom($from)
    {
        $this->from = $from;
    }

    /**
     * @param $sender
     */
    public function setSender($sender)
    {
        $this->sender = $sender;
    }

    /**
     * @param $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @param $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @param $html
     */
    public function setHtml($html)
    {
        $this->html = $html;
    }

    /**
     * @param $filename
     */
    public function addAttachment($filename)
    {
        $this->attachments[] = $filename;
    }

    /**
     * @param $filename
     * @return mixed
     */
    private function sbasename($filename)
    {
        return preg_replace('/^.+[\\\\\\/]/', '', $filename);
    }

    /**
     * @return bool
     */
    public function sendBySwift()
    {
        FileUtility::loadClassWithoutYii('swift/lib/swift_required.php');

        $sendto = $this->to;
        if (!is_array($this->to) && !empty($this->to)) {
            $sendto = array();
            $mails = explode(',', $this->to);
            $mails = array_filter($mails);
            foreach ($mails as $mkey => $mail) {
                $sendto[$mail] = '';
            }
        }

        $ccto = $this->cc;
        if (!is_array($this->cc) && !empty($this->cc)) {
            $ccto = array();
            $mails = explode(',', $this->cc);
            $mails = array_filter($mails);
            foreach ($mails as $mkey => $mail) {
                $ccto[$mail] = '';
            }
        }

        $trans_port = Swift_SmtpTransport::newInstance($this->hostname, $this->port);
        $trans_port->setUsername($this->username);
        $trans_port->setPassword($this->password);
        $mailer = Swift_Mailer::newInstance($trans_port);
        $message = Swift_Message::newInstance($this->subject);
        $message->setFrom(array($this->from => $this->sender));
        $message->setTo($sendto);
        if (!empty($ccto)) {
            $message->setCc($ccto);
        }
        $message->setBody($this->html, 'text/html');

        foreach ($this->attachments as $attachment) {
            if (file_exists($attachment)) {
                $handle = fopen($attachment, 'r');
                $content = fread($handle, filesize($attachment));
                fclose($handle);

                $filename = $this->sbasename($attachment);
                $attachment = Swift_Attachment::newInstance($content, $filename);
                //$attachment->setFilename("=?UTF-8?B?" . base64_encode($filename) . "?=");
                $message->attach($attachment);
            }
        }

        $numSent = $mailer->send($message);
        if ($numSent < 1) {
            Yii::log('Error: Send email failed! Subject[' . $this->subject . '] To[' . $this->to . ']', 'error',
                     'mail.error');

            return false;
        } else {
            Yii::log('Info: Send email success! Subject[' . $this->subject . '] To[' . $this->to . ']', 'info',
                     'mail.sended(' . $numSent . ')');

            return true;
        }
    }

    /**
     * @param $to
     * @param $subject
     * @param $body
     * @param array $attachments
     * @param int $is_html
     * @param string $cc
     * @return boolean
     */
    public function send($to, $subject, $body, $attachments = array(), $is_html = 1, $cc = '')
    {
        $to = $this->tryFixEmailAddress($to);
        $cc = $this->tryFixEmailAddress($cc);
        //验证Email地址格式
        if ($this->mailValidator($to)) {
            if (!empty($cc)) {
                if (!$this->mailValidator($cc)) {
                    //TODO:return a error
                    return false;
                }
            }

            $this->setTo($to);
            $this->setCc($cc);
            $this->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
            if ($is_html) {
                $this->setHtml($body);
            } else {
                $this->setText($body);
            }
            if (!empty($attachments)) {
                foreach ($attachments as $atta) {
                    $this->addAttachment($atta);
                }
            }

            return $this->sendBySwift();
        } else {
            //TODO:return a error
            return false;
        }
    }

    public static function sendToCustomer($to, $subject, $body, $attachments = array(), $is_html = 1, $cc = '')
    {

        $mail = Mail::getInstanceForCustomer();

        return $mail->send($to, $subject, $body, $attachments, $is_html, $cc);
    }

    public static function sendToOP($to, $subject, $body, $attachments = array(), $is_html = 1, $cc = '')
    {
        $mail = Mail::getInstanceForOP();

        return $mail->send($to, $subject, $body, $attachments, $is_html, $cc);
    }

    public static function sendToSupplier($to, $subject, $body, $attachments = array(), $is_html = 1, $cc = '')
    {
        $mail = Mail::getInstanceForSupplier();

        return $mail->send($to, $subject, $body, $attachments, $is_html, $cc);
    }

    public static function sendBySetting($mailSetting, $to, $subject, $body, $attachments = array(), $is_html = 1, $cc = '')
    {
        $mail = Mail::getInstanceBySetting($mailSetting);

       return $mail->send($to, $subject, $body, $attachments, $is_html, $cc);
    }

    /**
     * @param $email
     * @return string
     */
    public function mailValidator($email)
    {
        $singleEmail = strtok($email, ';,');
        while ($singleEmail !== false) {
            $validator = new CEmailValidator;
            if (!$validator->validateValue($singleEmail)) {
                //TODO:return a error
                Yii::log('Mail[' . $singleEmail . '] format is illegal!', 'warn', 'mail.warn');

                return false;
            }
            //echo "$singleEmail<br />";
            $singleEmail = strtok(";,");
        }

        return true;
    }

    private function tryFixEmailAddress($mail_str)
    {
        $mail_str = str_replace(':', ',', $mail_str);
        $mail_str = str_replace(';', ',', $mail_str);
        $mail_str = str_replace(' ', '', $mail_str);
        $mail_str = str_replace('　', '', $mail_str);
        $mail_str = str_replace('@.', '@', $mail_str);
        $mail_str = str_replace('..', '.', $mail_str);
        $mail_str = trim($mail_str, ',');

        return $mail_str;
    }

    public static function templateRender($template, $data = '')
    {
        if (file_exists($template) && is_file($template)) {
            extract($data);
            ob_start();
            require($template);
            $output = ob_get_contents();
            ob_end_clean();

            //echo $output;exit;
            return $output;
        } else {
            //TODO:throw exception
            return '';
        }
    }

    public static function getBody($data, $template_id)
    {
        //获取邮件发送模板
        $mailTemplate = HtNotifyTemplate::model()->findByPk($template_id);
        $templateUrl = dirname(Yii::app()->basePath) . Yii::app()->params['THEME_BASE_URL'] . $mailTemplate->path;

        return Mail::templateRender($templateUrl, $data); //渲染邮件模板
    }
}
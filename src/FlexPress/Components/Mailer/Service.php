<?php

namespace FlexPress\Components\Mailer;

class Service
{

    // =============
    // ! PROPERTIES
    // =============

    /**
     * @var \PHPMailer
     */
    protected $mailer;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var array
     */
    protected $context;

    /**
     * An array that holds the required properties
     * and if they have been set
     *
     * @var array
     */
    protected $requiredProperties;

    // ==============
    // ! CONSTRUCTOR
    // ==============

    public function __construct(\PHPMailer $mailer)
    {
        $this->mailer = $mailer;
        $this->requiredProperties = array(

            'To Address' => false,
            'From Address' => false,
            'Subject' => false,
            'Context' => false,
            'Template' => false

        );

    }

    // ==========
    // ! METHODS
    // ==========

    /**
     *
     * Setter for the mailer object
     *
     * @param \PHPMailer $mailer
     *
     * @return $this
     * @author Tim Perry
     *
     */
    public function setMailer(\PHPMailer $mailer)
    {
        $this->mailer = $mailer;
        return $this;
    }

    /**
     * Setter for the template
     *
     * @param string $template
     *
     * @return $this
     * @author Tim Perry
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        $this->requiredProperties['Template'] = true;

        return $this;
    }

    /**
     * Setter for the context
     *
     * @param string $context
     *
     * @return $this
     * @author Tim Perry
     */
    public function setContext($context)
    {
        $this->context = $context;
        $this->requiredProperties['Context'] = true;

        return $this;
    }

    /**
     *
     * Setter for the subject
     *
     * @param $subject
     * @return $this
     * @author Tim Perry
     *
     */
    public function setSubject($subject)
    {
        $this->mailer->Subject = $subject;
        $this->requiredProperties['Subject'] = true;

        return $this;
    }

    /**
     * Attaches a file
     *
     * @param $filename
     * @param $niceName
     *
     * @return bool
     * @author Tim Perry
     */
    public function addAttachment($filename, $niceName = '')
    {

        $this->mailer->AddAttachment($filename, $niceName);
        return $this;

    }

    /**
     *
     * Helper to add to address
     *
     * @param $address
     * @param $name
     * @return $this
     * @author Tim Perry
     *
     */
    public function addTo($address, $name)
    {
        $this->addAddress('to', $address, $name);
        $this->requiredProperties['To Address'] = true;

        return $this;
    }

    /**
     *
     * Helper to set the from address
     *
     * @param $address
     * @param $name
     * @return $this
     * @author Tim Perry
     *
     */
    public function setFrom($address, $name)
    {
        $this->addAddress('from', $address, $name);
        $this->requiredProperties['From Address'] = true;

        return $this;
    }

    /**
     *
     * Helper to add a bcc address
     *
     * @param $address
     * @param $name
     * @return $this
     * @author Tim Perry
     *
     */
    public function addBcc($address, $name)
    {
        $this->addAddress('bcc', $address, $name);
        return $this;
    }

    /**
     *
     * Helper to add a cc address
     *
     * @param $address
     * @param $name
     * @return $this
     * @author Tim Perry
     *
     */
    public function addCc($address, $name)
    {
        $this->addAddress('bcc', $address, $name);
        return $this;
    }

    /**
     *
     * Helper to add a reply to address
     *
     * @param $address
     * @param $name
     * @return $this
     * @author Tim Perry
     *
     */
    public function addReplyTo($address, $name)
    {
        $this->addAddress('replyto', $address, $name);
        return $this;
    }

    /**
     *
     * Can be used to add various types of address
     *
     * @param string $type
     * @param $address
     * @param string $name
     * @throws \InvalidArgumentException
     * @return $this
     * @author Tim Perry
     */
    public function addAddress($type, $address, $name = '')
    {

        if (empty($address)
            || filter_var($address, FILTER_VALIDATE_EMAIL) === false
        ) {

            $message = "Invalid address please provide a valid email address";
            throw new \InvalidArgumentException($message);

        }

        switch ($type) {

            default:
            case 'to':
                $this->mailer->AddAddress($address, $name);
                break;

            case 'from':
                $this->mailer->SetFrom($address, $name);
                break;

            case 'bcc':
                $this->mailer->AddBCC($address, $name);
                break;

            case 'cc':
                $this->mailer->AddCC($address, $name);
                break;

            case 'replyto':
                $this->mailer->AddReplyTo($address, $name);
                break;

        }

        return $this;

    }

    /**
     *
     * Validates that we have everything required to send
     * a valid email
     *
     * @throws \InvalidArgumentException
     * @author Tim Perry
     *
     */
    public function validate()
    {

        foreach ($this->requiredProperties as $property => $isset) {

            if (!$isset) {

                $message = "Invalid $property, please set a valid $property using its setter method.";
                throw new \InvalidArgumentException($message);

            }

        }

    }

    /**
     *
     * Returns the plain text body string for the given
     * html body string
     *
     * @param $htmlBody
     * @return string
     * @author Tim Perry
     *
     */
    protected function getPlainTextBody($htmlBody)
    {

        $plainText = str_replace("<br/>", "\r\n", $htmlBody);
        $plainText = str_replace("</p>", "\r\n", $plainText);
        $plainText = str_replace("</li>", "\r\n", $plainText);
        $plainText = strip_tags($plainText);

        return $plainText;

    }

    /**
     * Sends the email
     *
     * @throws \InvalidArgumentException
     * @internal param array $to_address
     * @internal param array $from_address
     * @return bool
     * @author Tim Perry
     */
    public function send()
    {

        $this->validate();

        $messageBody = \Timber::compile($this->template, $this->context);

        $this->mailer->AltBody = $this->getPlainTextBody($messageBody);
        $this->mailer->Body = $messageBody;

        $this->mailer->IsHTML(true);

        return $this->mailer->Send();

    }
}

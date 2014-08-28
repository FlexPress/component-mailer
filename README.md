
# FlexPress mailer component

- Please note that the mailer component uses Timber to render the template but it is not explicitly declared as a dependency in the composer file.

## Install via pimple

The mailer component only has one class, however it does use PHPMailer, lets create a simple pimple config for this:

```
$pimple['phpMailer'] = function() {
    return new \PHPMailer();
};

$pimple['mailerService'] = function($c) {
    return new Service($c['phpMailer']);
};
```

## Usage

First create a instance using pimple:

```
$mailer = $pimple['mailerService'];
```

What following is a very simple example, that is the minimum you need to use the component:

```
$mailer->setSubject('Some subject here')
    ->setContext(array(''))
    ->setTemplate('mailer/contact-form.html.twig')
    ->setFrom('tim@flexpress.github.io', 'Tim Perry')
    ->addTo('guy@somewhere.com', 'Some guy')
    ->send();
```

And this example has every method utilised:

```
$mailer->setSubject('Some subject here')
    ->setContext(array(''))
    ->setTemplate('mailer/contact-form.html.twig')
    ->setFrom('tim@flexpress.github.io', 'Tim Perry')
    ->addReplyTo('tim@flexpress.github.io', 'Tim Perry')
    ->addTo('guy@somewhere.com', 'Some guy')
    ->addCc('guyotherguy@somewhere.com', 'Some other guy')
    ->addBcc('hidden@somewhere.com', 'Some hidden guy')
    ->addAttachment('path/to/some/file.pdf', 'Some file.pdf')
    ->send();
```

## Public methods

- setMailer($mailer) - Setter method for the mailer.
- setTemplate($template) - Setter method for the template, pass it the path to your view and timber will render it.
- setContext($context) - Setter method for yout timber context.
- setSubject($subject) - Setter method for the email subject.
- addAttachment($filename, $niceName = '') - Adder method for attachments, provide a filepath and optionally a nicename.
- addTo($address, $name) - Adder method for a to address.
- setFrom($address, $name) - Setter method for the from address.
- addBcc($address, $name) - Adder method for a bcc address.
- addCc($address, $name) - Adder method for a cc address.
- addReplyTo($address, $name) - Adder method for a reply to address
- addAddress($type, $address, $name = '') - Used by the other methods to add address, e.g. $type can be to, from, bcc, cc and replyto.
- validate() - Validates that everything required to send the email it set.
- send() - Uses php mailer to send the email.


## Protected methods
- getPlainTextBody($htmlBody) - Used to get a plain text body for the given HTML input.

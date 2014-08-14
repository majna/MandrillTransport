MandrillTransport
=================

CakePHP email transport adapter for Mandrill (MailChimp) service https://www.mandrill.com

It can be used as drop-in replacement for SMTP transport to send transactional email. 
Mandrill's templates and merge variables are not supported.


Transactional email is sent using Mandrill's REST API. 
See https://mandrillapp.com/api/docs/messages.php.html#method=send

Requirements
------------

* CakePHP 2.x
* PHP >= 5.3

Installation
------------

__[Manual]__

To install the plugin, place the files in a directory "MandrillTransport/" in your "app/Plugin/" directory.

Include the following line in your app/Config/bootstrap.php to load the plugin in your application.

    CakePlugin::load('Users');

Configuration
-------------

Add to `App/Config/email.php`

    public $mandrill = array(
        'transport' => 'Mandrill',
        'uri' => 'https://mandrillapp.com/api/1.0/',
        'key' => 'YOUR_MANDRILL_API_KEY'
    );

Usage
-------------
    App::uses('CakeEmail', 'Network/Email');
    $email = new CakeEmail('mandrill');
    
For other options refer to http://book.cakephp.org/2.0/en/core-utility-libraries/email.html


License
-------

[The MIT License](http://opensource.org/licenses/mit-license.php)

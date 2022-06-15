<?php
/**
 * activation batch resend plugin for Craft CMS 3.x
 *
 * 
 *
 * @link      https://youclick.nl
  * @copyright Copyright (c) 2019 arif
 */
 
namespace youclickmedia\activationbatchresend\jobs;

use youclickmedia\activationbatchresend\ActivationBatchResend;

use Craft;
use craft\web;
use craft\queue\BaseJob;
use craft\services;

use yii\base\Exception;

use craft\elements\User;
use craft\helpers\App;
use craft\mail\Message;

class SendActivationMail extends BaseJob
{
	// form data
	public array $data;
	
	/**
     * @inheritdoc
     */
	public function execute($queue)
	{	
		// creation date of user		
		$days = ($this->data['creationDate']) ? $this->data['creationDate'] : 7;
		
		// get pending users
		$dateFilter = (new \DateTime( $days . ' days ago'))->format(\DateTime::ATOM);
		$users = \craft\elements\User::find()->status('pending')->dateCreated(" < {$dateFilter}")->all();
		
		// for testing
		// uncomment this lne to only send the mail to user with id 1/x
		//$users = \craft\elements\User::find()->status(null)->id(1)->all();
		
		// loop
		$totalUsers = count($users);
		$currentUser = 0;	
        		
	    foreach($users as $user) {
				    
		    // update unverifiedEmail if empty
			if(!$user->unverifiedEmail)
			{
				$user->unverifiedEmail = $user->email;
				Craft::$app->getElements()->saveElement($user);
			}
				    
		    // send mail
		    $result = $this->sendReminderMail($user);

	        if ($result) {
	            Craft::error('[activation mail] Activation reminder email sent successfully', __METHOD__);
	        }
	        else {
	            Craft::error('[activation mail] Unable to send activation reminder email', __METHOD__);
	        }
        		 	   
		    // update progress			
 			$this->setProgress($queue, $currentUser++ / $totalUsers);
		}
	}

	/**
     * @inheritdoc
     */		
	protected function sendReminderMail($user)
	{
		$view = Craft::$app->getView();
		$settings = Craft::$app->systemSettings->getSettings('email');
		
		// mail data			
		$subject = ($this->data['emailSubject']) ? $this->data['emailSubject'] : Craft::t('activation-batch-resend', 'Email Subject');
		$message = $this->data['emailMessage'];
		
		// create activation link
		$activationLink = Craft::$app->users->getEmailVerifyUrl($user);	

		// set template path
		$templatePath = Craft::$app->path->getSiteTemplatesPath();
		$view->setTemplatesPath($templatePath);
		
		// render template
		// activation-batch-resend/_mail
		$html = $view->renderTemplate('email/default', [
			'emailKey' => 'activation_reminder',
			'user' => $user, 
			'link' => $activationLink,
			'message' => $message
		]);
						
		// create message		
		$message = new Message();
		$message->setFrom([$settings['fromEmail'] => $settings['fromName']]);
		$message->setTo($user->email);
		//$message->setTo('arjan@skoften.net');
		$message->setSubject($subject);
        $message->setHtmlBody($html);
 
		// send mail
		$mailer = Craft::$app->getMailer();
		
        try {
            $result = $mailer->send($message);
        }
        catch (\Throwable $e){
            Craft::$app->getErrorHandler()->logException($e);
            $result = false;
        }

		return $result;	
	}
	
    /**
     * @inheritdoc
     */
    protected function defaultDescription(): string
    {
        return Craft::t('test', 'TestTask');
    }
    	
}

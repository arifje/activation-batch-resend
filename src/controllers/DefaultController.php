<?php
/**
 * Activation batch resend plugin for Craft CMS 3.x
 *
 * Resend activation email to all pending members
 *
 * @link      http://www.youclick.nl
 * @copyright Copyright (c) 2019 Arif
 */


namespace youclickmedia\activationbatchresend\controllers;

use youclickmedia\activationbatchresend\ActivationBatchResend;

use Craft;
use craft\web\Controller;
use craft\elements\User;
use craft\queue\BaseJob;
use youclickmedia\activationbatchresend\jobs\SendActivationMail;
use craft\mail\Message;

/**
 * @author    Arif
 * @package   ActivationBatchResend
 * @since     1.0.0
 */
class DefaultController extends Controller
{
	
    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    
    //protected $allowAnonymous = ['index', 'resend-activation'];

    public function actionResendActivation()
    {
	    Craft::error('[activation mail] actionResendActivation ', __METHOD__);

		// testing
		// uncomment this block to send a test mail to admin with id 1
		
		/*
		$user = Craft::$app->users->getUserById(1);
		//$activationLink = Craft::$app->users->getEmailVerifyUrl($user);	
		//return $activationLink;
		$result = Craft::$app->getUsers()->sendActivationEmail($user);
		return '1';
		*/
		
        /*
        $lastWeek = (new \DateTime('2 days ago'))->format(\DateTime::ATOM);
		$users = \craft\elements\User::find()->status('pending')->dateCreated(" < {$lastWeek}")->all();
		$users = \craft\elements\User::find()->status('pending')->all();
		$totalUsers = count($users);
		return $totalUsers;
	 	*/
	 
		// form data
       	$request = Craft::$app->getRequest();     	
       	
       	// allow POST action from utlity section only
       	if ($request->getIsPost() AND $request->getBodyParam('utility'))
       	{   	                  		
			// create job for queue
			$queue = Craft::$app->getQueue();
			$queue->ttr(7200);
			
			$jobId = $queue->push(new SendActivationMail([
				'description' => 'Resend activation mail for inactive users',
				'data' => [
					'creationDate' => $request->getBodyParam('creationDate'),
					'emailSubject' => $request->getBodyParam('emailSubject'),
					'emailMessage' => $request->getBodyParam('emailMessage')
				],
			]));

	        Craft::error('[activation mail] init job', __METHOD__);
		
		    Craft::$app->getSession()->setNotice('Resending started, added task to queue (' . $jobId .')');
								
		    return $this->redirectToPostedUrl();	
		}
		else
		{
			return "Limited access to controller";
		}   
    }
}

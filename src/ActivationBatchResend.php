<?php
/**
 * Activation batch resend plugin for Craft CMS 3.x
 *
 * Resend activation email to all pending members
 *
 * @link      http://www.youclick.nl
 * @copyright Copyright (c) 2019 Arif
 */

namespace youclickmedia\activationbatchresend;
use youclickmedia\activationbatchresend\utilities\ActivationBatchResendUtility as ActivationBatchResendUtilityUtility;


use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterComponentTypesEvent;

use craft\services\UserPermissions;
use craft\services\Utilities;

use yii\base\Event;

/**
 * Class ActivationBatchResend
 *
 * @author    Arif
 * @package   ActivationBatchResend
 * @since     1.0.0
 *
 */
class ActivationBatchResend extends Plugin
{

    /**
     * @var ActivationBatchResend
     */

    public static $plugin;
    public $schemaVersion = '1.0.0';

    /**
     * @inheritdoc
     */

    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['siteActionTrigger1'] = 'activation-batch-resend/default';
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['cpActionTrigger1'] = 'activation-batch-resend/default/do-something';
            }
        );

        // Register utility
        Event::on(
            Utilities::class,
            Utilities::EVENT_REGISTER_UTILITY_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = ActivationBatchResendUtilityUtility::class;
            }
        );
        
        
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        Craft::info(
            Craft::t(
                'activation-batch-resend',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }


}

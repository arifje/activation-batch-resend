<?php
/**
 * @link      https://craftcampaign.com
 * @copyright Copyright (c) PutYourLightsOn
 */

namespace youclickmedia\activationbatchresend\utilities;

use Craft;
use craft\base\Utility;
use youclickmedia\activationbatchresend\ActivationBatchResend;

class ActivationBatchResendUtility extends Utility
{
    // Static
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('activation-batch-resend', 'Activation resend');
    }

    /**
     * @inheritdoc
     */
    public static function id(): string
    {
         return 'activationbatchresend-activation-batch-resend-utility';
    }

    /**
     * @inheritdoc
     */
    public static function iconPath(): string
    {
        return Craft::getAlias('@vendor/youclickmedia/activationbatchresend/src/icon-mask.svg');
    }

    /**
     * @inheritdoc
     */
    public static function contentHtml(): string
    {
         return Craft::$app->getView()->renderTemplate('activation-batch-resend/_utility', [
            'settings' => ActivationBatchResend::$plugin->getSettings(),
        ]);
    }
}

<?php

/*
 * This file is part of phrasea-4.0.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Phrasea\Core\Event\Subscriber;

use Alchemy\Phrasea\BaseApplication;
use Alchemy\Phrasea\Core\Event\User\DeletedEvent;
use Alchemy\Phrasea\Core\Event\User\UserEvents;
use Alchemy\Phrasea\Model\Entities\WebhookEvent;
use Alchemy\Phrasea\Model\Manipulator\WebhookEventManipulator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WebhookUserEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var BaseApplication
     */
    private $app;

    /**
     * @param BaseApplication $application
     */
    public function __construct(BaseApplication $application)
    {
        $this->app = $application;
    }

    /**
     * @param DeletedEvent $event
     */
    public function onUserDeleted(DeletedEvent $event)
    {
        /** @var WebhookEventManipulator $manipulator */
        $manipulator = $this->app['manipulator.webhook-event'];

        $manipulator->create(WebhookEvent::USER_DELETED, WebhookEvent::USER_DELETED_TYPE, [
            'user_id' => $event->getUserId(),
            'email' => $event->getEmailAddress(),
            'login' => $event->getLogin()
        ]);
    }

    public static function getSubscribedEvents()
    {
        return [
            UserEvents::DELETED => 'onUserDeleted'
        ];
    }
}

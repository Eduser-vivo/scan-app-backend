<?php
namespace App\EventSubscriber;

use App\Entity\Fiche;
use Symfony\Component\HttpKernel\KernelEvents;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UtilisateurEntitySubscriber implements EventSubscriberInterface
{

    /**
     * @var TokenStorageInterface
     */
    private $tokenSotrage;

    public function __construct(TokenStorageInterface $tokenSotrage)
    {
        $this->tokenStorage = $tokenSotrage;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['getAuthenticatedUser', EventPriorities::PRE_WRITE]
        ];
    }

    public function getAuthenticatedUser(GetResponseForControllerResultEvent $event)
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        /**
         * @var UserInterface $utilisateur
         */
        $utilisateur = $this->tokenSotrage->getToken()->getUser();

        if(!$entity instanceof Fiche || Request::METHOD_POST)
        {
            return;
        }
        $entity->setUtilisateur($utilisateur);
    }
}
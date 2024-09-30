<?php
namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExerciceValidationSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        ];
    }

    public function onPostSubmit(FormEvent $event)
    {
        $exercice = $event->getData();
        $type = $exercice->getType();
        $contenu = json_decode($exercice->getContenu(), true);
        $solution = json_decode($exercice->getSolution(), true);

        switch ($type) {
            case 'OQ':
                if (!is_array($contenu) || count($contenu) !== 1 || !is_array($solution) || count($solution) !== 2) {
                    throw new HttpException(400, 'Invalid format for type OQ.');
                }
                break;
            case 'FTB':
                if (!is_array($contenu) || count($contenu) !== 1 || !is_array($solution) || count($solution) < 1) {
                    throw new HttpException(400, 'Invalid format for type FTB.');
                }
                break;
            case 'QCM':
                if (!is_array($contenu) || count($contenu) !== 5 || !is_array($solution) || count($solution) !== 1) {
                    throw new HttpException(400, 'Invalid format for type QCM.');
                }
                break;
            default:
                throw new HttpException(400, 'Unknown exercise type.');
        }

        $exercice->setContenu(json_encode($contenu));
        $exercice->setSolution(json_encode($solution));
    }
}

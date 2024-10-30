<?php

namespace AppFrontendBundle\Controller;

use AppBundle\Enum\CourseType;
use AppBundle\Enum\PaymentMethod;
use AppBundle\Service\InflectionManager;
use AppBundle\Service\QueryExecutor;
use AppFrontendBundle\Form\Type\DiscountCouponType;
use AppFrontendBundle\Form\Type\OrderConfirmationType;
use AppFrontendBundle\Form\Type\OrderSeminarDatesType;
use AppFrontendBundle\Form\Type\OrderSetupType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use AppBundle\Entity\Region;
use AppBundle\Entity\Customer;
use AppBundle\Entity\SeminarParticipant;
use AppBundle\Data\Query\DiscountCoupon\DiscountCouponListQuery;
use AppBundle\Exception\OrderException;
use AppFrontendBundle\Model\Cart;
use AppFrontendBundle\Service\CartManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/cart')]
class CartController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator,
        private InflectionManager   $inflectionManager,
    )
    {
    }

    private function handleParticipantForm(
        RequestStack $requestStack,
        Cart $cart, FormInterface $participantForm, CartManager $cartManager): bool
    {
        if ($requestStack->getCurrentRequest()->isMethod('POST')) {
            $participantForm->handleRequest($requestStack->getCurrentRequest());

            if ($participantForm->isValid()) {

                $data = $participantForm->getData();
                $participants = $data[0]->getParticipants();

                $this->inflectParticipants($participants);

                $cartManager->updateCart($requestStack, $cart);

                return true;
            }
        }

        return false;
    }




    private function inflectParticipants(array $participants): void
    {
        foreach ($participants as $participant) {
            $this->inflectionManager->inflectPerson($participant);
        }
    }
}

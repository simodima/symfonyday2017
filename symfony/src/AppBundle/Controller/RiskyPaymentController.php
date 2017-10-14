<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Form\FormInterface;
use Payment\PaymentInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;

class RiskyPaymentController extends Controller
{

    public function getPaymentForm(string $route): FormInterface
    {
        $action      = $this->generateUrl($route);        
        $formFactory = $this->get('payment.form.factory');
        
        return $formFactory->buildWithAction($action);
    }

    /**
     * @Route("/sync-pay", name="risky_pay")
     */
    public function riskyPayAction(): Response
    {
        return $this->render('payment/risky.html.twig', [
            'form' => $this->getPaymentForm('submit_payment')->createView()
        ]);
    }

    /**
     * @Route("/pay", name="submit_payment")
     */
    public function submitPaymentAction(Request $request): Response
    {
        $form = $this->getPaymentForm('submit_payment')->handleRequest($request);
        $data = $form->getData();

        /** @var PaymentInterface $pay */
        $pay = $this->get('payment.pay.sync');
        
        return ResponseConverter::fromPsr7(
            $pay->pay($data)
        );
    }
}

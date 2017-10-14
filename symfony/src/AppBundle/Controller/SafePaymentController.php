<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use GuzzleHttp\Psr7\Request as GuzzleRequest;
use Symfony\Component\Form\FormInterface;

class SafePaymentController extends Controller
{
    public function getPaymentForm(string $route): FormInterface
    {
        $action      = $this->generateUrl($route);        
        $formFactory = $this->get('payment.form.factory');
        
        return $formFactory->buildWithAction($action);
    }

    /**
     * @Route("/async-pay", name="safe_pay")
     */
    public function safePayAction(Request $request)
    {
        return $this->render('payment/safe.html.twig', [
            'form' => $this->getPaymentForm('queue_payment')->createView()
        ]);
    }
    
    /**
     * @Route("/queue-payment", name="queue_payment")
     */
    public function queuePaymentAction(Request $request)
    {
        $form = $this->getPaymentForm('queue_payment')->handleRequest($request);
        $data = $form->getData();
        /** @var PaymentInterface $pay */
        $pay = $this->get('payment.pay.async');

        return ResponseConverter::fromPsr7($pay->pay($data));
    }
}
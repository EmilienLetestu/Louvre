<?php
namespace EL\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
/**
 * Created by PhpStorm.
 * User: Emilien
 * Date: 16/05/2017
 * Time: 14:51
 */
class HomeController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $this->get('el_booking.tools')->deleteExpiredFile('-1 day');
        $temp_order_manager = $this->get('el_booking.tempOrderManager')->checkStatusAndProcess(
            $request,
            $timezone       = 'Europe/Paris',
            $pm_access      = 14
            ,$booking_limit = 1000,
            $prefix         = 'Commande n°: '
        );

        return $this->render('ELBookingBundle:Home:index.html.twig', [
            'booking_status_form' => $temp_order_manager[0],
            'disclaimer'          => $temp_order_manager[1]
        ]);
    }
}
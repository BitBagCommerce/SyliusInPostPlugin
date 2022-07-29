<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusInPostPlugin\Form\Type;

use BitBag\SyliusInPostPlugin\Api\WebClientInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

final class ShippingGatewayType extends AbstractType
{
    public const ENVIRONMENT_CHOICES = [
        'bitbag_sylius_inpost_plugin.ui.sandbox' => WebClientInterface::SANDBOX_ENVIRONMENT,
        'bitbag_sylius_inpost_plugin.ui.production' => WebClientInterface::PRODUCTION_API_ENDPOINT,
    ];

    public const SERVICE_CHOICES = [
        'bitbag_sylius_inpost_plugin.main_service.inpost_locker_standard' => WebClientInterface::INPOST_LOCKER_STANDARD_SERVICE,
        'bitbag_sylius_inpost_plugin.main_service.inpost_locker_pass_thru' => WebClientInterface::INPOST_LOCKER_PASS_THRU_SERVICE,
        'bitbag_sylius_inpost_plugin.main_service.inpost_courier_standard' => WebClientInterface::INPOST_COURIER_STANDARD_SERVICE,
        'bitbag_sylius_inpost_plugin.main_service.inpost_courier_express_1000' => WebClientInterface::INPOST_COURIER_EXPRESS_1000_SERVICE,
        'bitbag_sylius_inpost_plugin.main_service.inpost_courier_express_1200' => WebClientInterface::INPOST_COURIER_EXPRESS_1200_SERVICE,
        'bitbag_sylius_inpost_plugin.main_service.inpost_courier_express_1700' => WebClientInterface::INPOST_COURIER_EXPRESS_1700_SERVICE,
        'bitbag_sylius_inpost_plugin.main_service.inpost_courier_local_standard' => WebClientInterface::INPOST_COURIER_LOCAL_STANDARD_SERVICE,
        'bitbag_sylius_inpost_plugin.main_service.inpost_courier_local_express' => WebClientInterface::INPOST_COURIER_LOCAL_EXPRESS_SERVICE,
        'bitbag_sylius_inpost_plugin.main_service.inpost_courier_local_super_express' => WebClientInterface::INPOST_COURIER_LOCAL_SUPER_EXPRESS_SERVICE,
    ];

    public const ADDITIONAL_SERVICE_CHOICES = [
        'bitbag_sylius_inpost_plugin.additional_service.sms' => WebClientInterface::SMS_ADDITIONAL_SERVICE,
        'bitbag_sylius_inpost_plugin.additional_service.email' => WebClientInterface::EMAIL_ADDITIONAL_SERVICE,
        'bitbag_sylius_inpost_plugin.additional_service.saturday' => WebClientInterface::SATURDAY_ADDITIONAL_SERVICE,
        'bitbag_sylius_inpost_plugin.additional_service.rod' => WebClientInterface::ROD_ADDITIONAL_SERVICE,
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('access_token', TextType::class, [
                'label' => 'bitbag_sylius_inpost_plugin.ui.access_token',
                'constraints' => [
                    new NotBlank([
                        'message' => 'bitbag_sylius_inpost_plugin.ui.organization_id.not_blank',
                        'groups' => 'bitbag',
                    ]),
                ],
            ])
            ->add('organization_id', TextType::class, [
                'label' => 'bitbag_sylius_inpost_plugin.ui.organization_id',
                'constraints' => [
                    new NotBlank([
                        'message' => 'bitbag_sylius_inpost_plugin.ui.organization_id.not_blank',
                        'groups' => 'bitbag',
                    ]),
                ],
            ])
            ->add('environment', ChoiceType::class, [
                'label' => 'bitbag_sylius_inpost_plugin.ui.environment',
                'choices' => self::ENVIRONMENT_CHOICES,
                'constraints' => [
                    new NotBlank([
                        'message' => 'bitbag_sylius_inpost_plugin.ui.environment.not_blank',
                        'groups' => 'bitbag',
                    ]),
                ],
            ])
            ->add('service', ChoiceType::class, [
                'label' => 'bitbag_sylius_inpost_plugin.ui.service',
                'choices' => self::SERVICE_CHOICES,
                'constraints' => [
                    new NotBlank([
                        'message' => 'bitbag_sylius_inpost_plugin.ui.not_blank',
                        'groups' => 'bitbag',
                    ]),
                ],
            ])
            ->add('insurance_amount', NumberType::class, [
                'label' => 'bitbag_sylius_inpost_plugin.ui.insurance_amount',
                'required' => false,
            ])
            ->add('cod_payment_method_code', TextType::class, [
                'label' => 'bitbag_sylius_inpost_plugin.ui.cod_payment_method_code',
                'required' => false,
            ])
            ->add('is_return', CheckboxType::class, [
                'label' => 'bitbag_sylius_inpost_plugin.ui.is_return',
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event): void {
                $data = $event->getData();
                if (isset($data['organization_id'])) {
                    $event
                        ->getForm()
                        ->add('is_quick_return', CheckboxType::class, [
                            'label' => 'bitbag_sylius_inpost_plugin.ui.is_quick_return',
                        ])
                    ;
                }
            })
            ->add('additional_services', ChoiceType::class, [
                'label' => 'bitbag_sylius_inpost_plugin.ui.additional_services',
                'choices' => self::ADDITIONAL_SERVICE_CHOICES,
                'expanded' => true,
                'multiple' => true,
                'required' => false,
            ])
        ;
    }
}

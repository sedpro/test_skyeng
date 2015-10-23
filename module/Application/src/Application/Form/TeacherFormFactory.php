<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TeacherFormFactory implements FactoryInterface
{
    /**
     * Create a form
     *
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return \Zend\Form\Form
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $formFactory = new \Zend\Form\Factory;

        $form = $formFactory->createForm(
            array(
                'elements' => [
                    [
                        'spec' => [
                            'type' => \Zend\Form\Element\Text::class,
                            'name' => 'name',
                            'options' => array(
                                'label' => 'Name',
                            ),
                            'attributes' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                    [
                        'spec' => [
                            'type' => \Zend\Form\Element\Text::class,
                            'name' => 'phone',
                            'options' => array(
                                'label' => 'Phone'
                            ),
                            'attributes' => array(
                                'type' => 'tel',
                                'class' => 'form-control',
                            )
                        ],
                    ],
                    [
                        'spec' => [
                            'type' => \Zend\Form\Element\Radio::class,
                            'name' => 'gender',
                            'options' => [
                                'label' => 'Gender',
                                'value_options' => \Application\Entity\Teacher::$gender_text,
                                'label_attributes' => [
                                    'class' => 'radio_button',
                                ],
                            ],
                            'attributes' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                    [
                        'spec' => [
                            'type' => \Zend\Form\Element\Csrf::class,
                            'name' => 'security',
                        ],
                    ],
                ],
                'input_filter' => [
                    'name' => [
                        'required' => true,
                        'filters'  => [
                            ['name' => \Zend\Filter\StringTrim::class],
                        ],
                    ],
                    'phone' => [
                        'required' => true,
                        'filters'  => [
                            ['name' => \Zend\Filter\StringTrim::class],
                        ],
                    ],
                ],
            )
        );

        $form->setAttribute('class', 'form-horizontal');

        return $form;
    }
}
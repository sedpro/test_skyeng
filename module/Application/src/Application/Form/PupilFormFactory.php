<?php

namespace Application\Form;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class PupilFormFactory implements FactoryInterface
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
                            'type' => \Zend\Form\Element\Email::class,
                            'name' => 'email',
                            'options' => [
                                'label' => 'Email address',
                            ],
                            'attributes' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                    [
                        'spec' => [
                            'type' => \Zend\Form\Element\Date::class,
                            'name' => 'birthday',
                            'options' => [
                                'label' => 'Birthday',
                            ],
                            'attributes' => [
                                'class' => 'form-control datepicker',
                            ],
                        ],
                    ],
                    [
                        'spec' => [
                            'type' => \Zend\Form\Element\Radio::class,
                            'name' => 'level',
                            'options' => [
                                'label' => 'Level',
                                'label_attributes' => [
                                    'class' => 'radio_button',
                                ],
                                'value_options' => \Application\Entity\Pupil::$level_text,
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
                    'name' => array(
                        'required' => true,
                        'filters'  => [
                            ['name' => \Zend\Filter\StringTrim::class],
                        ],
                    ),
                    'email' => [
                        'required' => true,
                        'filters'  => [
                            ['name' => \Zend\Filter\StringTrim::class],
                        ],
                        'validators' => [
                            new \Zend\Validator\EmailAddress(),
                        ],
                    ],
                    'birthday' => [
                        'required' => true,
                    ],
                    'level' => [
                        'required' => true,
                    ],
                ],
            )
        );

        $form->setAttribute('class', 'form-horizontal');

        return $form;
    }
}
<?php

class Application_Form_Registration extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');

        $this->addElement('text', 'login', array(
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'validator' => 'NotEmpty',
                    'options' => array(
                        'messages'=> 'Пожалуйста, введите свой логин'
                    ),
                    'breakChainOnFailure' => true
                ),
                array(
                    'validator' => 'StringLength',
                    'options' => array(
                        'min' => 3,
                        'max' => 100,
                        'messages'=> array('stringLengthTooShort' => 'Пожалуйста, введите логин (от 3 символов)')
                    ),
                    'breakChainOnFailure' => true
                )
            )
        ));

        $this->addElement('password', 'password', array(
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'validator' => 'NotEmpty',
                    'options' => array(
                        'messages'=> 'Пожалуйста, введите свой пароль'
                    ),
                    'breakChainOnFailure' => true
                )
            )
        ));

        $this->addElement('text', 'email', array(
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'validator' => 'NotEmpty',
                    'options' => array(
                        'messages'=> 'Пожалуйста, введите email'
                    ),
                    'breakChainOnFailure' => true
                ),
                array(
                    'validator' => 'EmailAddress',
                    'options' => array(
                        'messages'=> 'Пожалуйста, введите правильный email'
                    ),
                    'breakChainOnFailure' => true
                )
            )
        ));

    }
}
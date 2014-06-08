<?php

class Application_Form_Auth extends Zend_Form
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

    }
}
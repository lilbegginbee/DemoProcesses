<?php

class Application_Form_ProcessAdd extends Zend_Form
{
    public function init()
    {
        $this->setMethod('post');

        $this->addElement('text', 'title', array(
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'validator' => 'NotEmpty',
                    'options' => array(
                        'messages'=> 'Пожалуйста, введите название процесса'
                    ),
                    'breakChainOnFailure' => true
                ),
                array(
                    'validator' => 'StringLength',
                    'options' => array(
                        'min' => 3,
                        'max' => 100,
                        'messages'=> 'Пожалуйста, введите название процесса (не менее 5 символов)'
                    ),
                    'breakChainOnFailure' => true
                )
            )
        ));

        $this->addElement('text', 'duration', array(
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array(
                    'validator' => 'NotEmpty',
                    'options' => array(
                        'messages'=> 'Пожалуйста, введите длительность процесса в секундах'
                    ),
                    'breakChainOnFailure' => true
                ),
                array(
                    'validator' => 'Int',
                    'options' => array(
                        'messages'=> 'Длительность должна быть в секундах'
                    ),
                    'breakChainOnFailure' => true
                ),
                array(
                    'validator' => 'GreaterThan',
                    'options' => array(
                        'min' => 0,
                        'messages'=> 'Длительность должна быть больше нуля'
                    ),
                    'breakChainOnFailure' => true
                )
            )
        ));


    }
}
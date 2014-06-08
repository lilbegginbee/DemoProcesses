<?php
class Corvita_View_Helper_ElementsErrors extends Zend_View_Helper_Abstract
{
	public function ElementsErrors($form)
	{
        $content = '';
        if ($form->hasErrors()) {
            foreach ($form->getElements() AS $element => $obj) {
                $messages = $obj->getMessages();
                $message = is_array($messages) ? reset($messages) : $messages;
                if ($message) {
                    $content .= "<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>&times;</button>$message</div>";
                }
            }

        }

        return $content;
	}
}
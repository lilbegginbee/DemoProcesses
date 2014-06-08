<?php
class Corvita_View_Helper_FormErrors extends Zend_View_Helper_Abstract
{
	public function FormErrors($form)
	{
        $content = '';
        if ($form->hasErrors()) {
            $elements = $form->getElements();
            $elements = array_keys($elements);
            foreach ($form->getMessages() AS $element => $messages) {
                if (!in_array((string)$element, $elements)) {
                    $message = is_array($messages) ? reset($messages) : $messages;
                    if ($message) {
                        $content .= "$message";
                    }
                }
            }

            if ($content != '') {
                $content = "<div class='alert alert-danger'><button type='button' class='close' data-dismiss='alert'>&times;</button>$content</div>";
            }
        }

        return $content;
	}
}
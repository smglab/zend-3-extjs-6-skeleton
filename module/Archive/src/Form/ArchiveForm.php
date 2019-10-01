<?php

namespace Archive\Form;

use Zend\InputFilter;
use Zend\InputFilter\Input;
use Zend\Form\Element;
use Zend\Form\Form;

class ArchiveForm extends Form
{
    public function __construct($name = 'archive', $options = array())
    {
        parent::__construct($name, $options);

        $this->add([
            'name' => 'id',
            'attributes' => [
                'type'  => 'hidden',
            ],
        ]);

        $this->add([
            'name' => 'filedelete',
            'attributes' => [
                'type'  => 'hidden',
            ],
        ]);

        $this->add([
            'name' => 'name',
            'attributes' => [
                'type'  => 'text',
            ],
            'options' => [
                'label' => 'Archive name',
            ],
        ]);

        $this->add([
            'name' => 'files',
            'attributes' => [
                'type'  => Element\File::class,
                'multiple' => true,
                'isArray' => true,
            ],
            'options' => [
                'label' => 'Files',
            ],
        ]);
        $this->addInputFilter();
    }

    public function addInputFilter()
    {
        $inputFilter = new InputFilter\InputFilter();

        $name = new Input('name');
        $name->setRequired(true);
        $inputFilter->add($name);

        $fileInput = new InputFilter\FileInput('files');
        $fileInput->setRequired(false);

        $fileInput->getValidatorChain()
            ->attachByName('filesize',      ['max' => 10485760])
            ->attachByName('filemimetype',  [
                'mimeType' => 'image/jpeg,'.
                    'application/pdf,'.
                    'application/msword,'.
                    'application/vnd.ms-excel,'.
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document,'.
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ]);

        $fileInput->getFilterChain()->attachByName(
            'filerenameupload',
            [
                'target'    => './data/tmpuploads/',
                'randomize' => true,
            ]
        );
        $inputFilter->add($fileInput);

        $this->setInputFilter($inputFilter);
    }
}

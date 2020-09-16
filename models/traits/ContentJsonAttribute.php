<?php


namespace app\models\traits;


use yii\helpers\Json;

trait ContentJsonAttribute
{
    protected $_content;

    public function contentVersion()
    {
        return 1;
    }

    public function contentJsonFieldName()
    {
        return 'content_json';
    }

    public function contentFields()
    {
        return [];
    }

    public function getContent($section = null)
    {
        if(!is_array($this->_content)) {
            $this->_content = $this->{$this->contentJsonFieldName()} ? Json::decode($this->{$this->contentJsonFieldName()}) : ['v' => $this->contentVersion()];
        }

        return $section ? ($this->_content[$section] ?? null) : $this->_content;
    }
    public function setContent($section = null, $value = null)
    {
        $this->getContent();
        if($section) {
            $this->_content[$section] = $value;
        }
        $this->{$this->contentJsonFieldName()} = Json::encode($this->_content);
    }

    public function __get($name) {
        $name2key = array_flip($this->contentFields());
        if(isset($name2key[$name])) {
            return $this->getContent($name2key[$name]);
        }

        return parent::__get($name);
    }

    public function __set($name, $value) {
        $name2key = array_flip($this->contentFields());
        if(isset($name2key[$name])) {
            $this->setContent($name2key[$name], $value);
        } else {
            parent::__set($name, $value);
        }
    }

}
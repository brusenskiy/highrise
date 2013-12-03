<?php

namespace Highrise;

class Tag
{
    /** @var int */
    protected $_id;

    /** @var string */
    protected $_name;

    /** @var int */
    protected $_subjectId;

    /** @var string */
    protected $_subjectType;

    /**
     * @param int $_id
     */
    public function setId($_id)
    {
        $this->_id = $_id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * @param string $_name
     */
    public function setName($_name)
    {
        $this->_name = $_name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @param int $_subjectId
     */
    public function setSubjectId($_subjectId)
    {
        $this->_subjectId = $_subjectId;
    }

    /**
     * @return int
     */
    public function getSubjectId()
    {
        return $this->_subjectId;
    }

    /**
     * @param string $_subjectType
     */
    public function setSubjectType($_subjectType)
    {
        $this->_subjectType = $_subjectType;
    }

    /**
     * @return string
     */
    public function getSubjectType()
    {
        return $this->_subjectType;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getPushXml()
    {
        if ($this->getId()) {
            throw new \Exception('Only creation allowed');
        }

        return '<name>' . htmlspecialchars($this->getName()) . '</name>';
    }

    /**
     * @param \SimpleXMLElement $_tag
     * @return Tag
     */
    public static function factory(\SimpleXMLElement $_tag)
    {
        $tag = new self;

        $tag->setId((int) $_tag->{'id'});
        $tag->setName((string) $_tag->{'name'});
//        $tag->setSubjectType((string) $_tag->{'subject-type'});
//        $tag->setSubjectId((string) $_tag->{'subject-id'});

        return $tag;
    }
}

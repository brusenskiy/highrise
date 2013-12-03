<?php

namespace Highrise;

class Note
{
    /** @var int */
    protected $_id;

    /** @var string */
    protected $_body;

    /** @var int */
    protected $_subjectId;

    /** @var string */
    protected $_subjectType;

    /** @var int */
    protected $_authorId;

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
     * @param string $_body
     */
    public function setBody($_body)
    {
        $this->_body = $_body;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->_body;
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
     * @param int $_authorId
     */
    public function setAuthorId($_authorId)
    {
        $this->_authorId = $_authorId;
    }

    /**
     * @return int
     */
    public function getAuthorId()
    {
        return $this->_authorId;
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

        $xml  = '<note>';
        $xml .= '<body>' . htmlspecialchars($this->getBody()) . '</body>';
        $xml .= '<visible-to>Owner</visible-to>';

        $xml .= '<subject-id type="integer">';
        $xml .= $this->getSubjectId();
        $xml .= '</subject-id>';

        $xml .= '<subject-type>';
        $xml .= $this->getSubjectType();
        $xml .= '</subject-type>';

        if ($this->getAuthorId()) {
            $xml .= '<author-id type="integer">';
            $xml .= $this->getAuthorId();
            $xml .= '</author-id>';
        }

        $xml .= '</note>';
        return $xml;
    }

    /**
     * @param \SimpleXMLElement $_note
     * @return Note
     */
    public static function factory(\SimpleXMLElement $_note)
    {
        $note = new self;

        $note->setId((int) $_note->{'id'});
        $note->setBody((string) $_note->{'body'});
        $note->setSubjectType((string) $_note->{'subject-type'});
        $note->setSubjectId((string) $_note->{'subject-id'});
        $note->setAuthorId((string) $_note->{'author-id'});

        return $note;
    }
}

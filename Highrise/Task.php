<?php

namespace Highrise;

class Task
{
    /** @var int */
    protected $_id;

    /**
     * Название
     *
     * @var string
     */
    protected $_body;

    /**
     * Временные рамки
     *
     * @var string
     */
    protected $_frame;

    /**
     * Доступно ли всем
     *
     * @var bool
     */
    protected $_isPublic;

    /**
     * Дедлайн
     *
     * @var int
     */
    protected $_dueAt;

    /**
     * Категория
     *
     * @var int
     */
    protected $_categoryId;

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
     * @param string $_frame
     */
    public function setFrame($_frame)
    {
        $this->_frame = $_frame;
    }

    /**
     * @return string
     */
    public function getFrame()
    {
        return $this->_frame;
    }

    /**
     * @param null|bool $_isPublic
     * @return bool
     */
    public function isPublic($_isPublic = null)
    {
        if ($_isPublic === null) {
            return (bool) $this->_isPublic;

        } else {
            $this->_isPublic = (bool) $_isPublic;
        }
    }

    /**
     * @param int $_dueAt
     */
    public function setDueAt($_dueAt)
    {
        $this->_dueAt = $_dueAt;
    }

    /**
     * @return int
     */
    public function getDueAt()
    {
        return $this->_dueAt;
    }

    /**
     * @param int $_categoryId
     */
    public function setCategoryId($_categoryId)
    {
        $this->_categoryId = $_categoryId;
    }

    /**
     * @return int
     */
    public function getCategoryId()
    {
        return $this->_categoryId;
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
     * @param bool $_notify
     * @return string
     * @throws \Exception
     */
    public function getPushXml($_notify = false)
    {
        if ($this->getId()) {
            throw new \Exception('Only creation allowed');
        }

        $xml  = '<task>';
        $xml .= '<body>' . htmlspecialchars($this->getBody()) . '</body>';
        $xml .= '<frame>' . $this->getFrame() . '</frame>';

        $xml .= '<public type="boolean">';
        $xml .= $this->isPublic() ? 'true' : 'false';
        $xml .= '</public>';

        $xml .= '<notify type="boolean">';
        $xml .= $_notify ? 'true' : 'false';
        $xml .= '</notify>';

        if ($this->getCategoryId()) {
            $xml .= '<category-id type="integer">';
            $xml .= $this->getCategoryId();
            $xml .= '</category-id>';
        }

        $xml .= '<subject-id type="integer">';
        $xml .= $this->getSubjectId();
        $xml .= '</subject-id>';

        $xml .= '<subject-type>';
        $xml .= $this->getSubjectType();
        $xml .= '</subject-type>';

        $xml .= '</task>';
        return $xml;
    }

    /**
     * @param \SimpleXMLElement $_task
     * @return Task
     */
    public static function factory(\SimpleXMLElement $_task)
    {
        $task = new self;

        $task->setId((int) $_task->{'id'});
        $task->setCategoryId((int) $_task->{'category-id'});
        $task->setBody((string) $_task->{'body'});
        $task->setDueAt(strtotime((string) $_task->{'due-at'}));
        $task->setFrame((string) $_task->{'frame'});
        $task->isPublic('true' == $_task->{'public'});
        $task->setSubjectType((string) $_task->{'subject-type'});
        $task->setSubjectId((string) $_task->{'subject-id'});

        return $task;
    }
}

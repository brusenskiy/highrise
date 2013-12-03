<?php

/**
 * @todo Структура телефонов и адресов не соответствует структуре из Highrise:
 * реализация упрощена для реализации только добавления записей.
 */

namespace Highrise;

class Person
{
    /** @var int */
    protected $_id;

    /** @var string */
    protected $_firstName;

    /** @var string */
    protected $_lastName;

    /** @var string */
    protected $_email;

    /** @var string */
    protected $_phone;

    /** @var array */
    protected $_customFields = array();

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
     * @param string $_email
     */
    public function setEmail($_email)
    {
        $this->_email = $_email;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * @param string $_firstName
     */
    public function setFirstName($_firstName)
    {
        $this->_firstName = $_firstName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->_firstName;
    }

    /**
     * @param string $_lastName
     */
    public function setLastName($_lastName)
    {
        $this->_lastName = $_lastName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->_lastName;
    }

    /**
     * @param string $_phone
     */
    public function setPhone($_phone)
    {
        $this->_phone = $_phone;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->_phone;
    }

    /**
     * @param int $_id
     * @param string $_value
     */
    public function setCustomField($_id, $_value)
    {
        $this->_customFields[$_id] = $_value;
    }

    /**
     * @return array
     */
    public function getCustomFields()
    {
        return $this->_customFields;
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

        $xml  = '<person>';
        $xml .= '<last-name>' . $this->getLastName() . '</last-name>';

        if ($this->getFirstName()) {
            $xml .= '<first-name>';
            $xml .= $this->getFirstName();
            $xml .= '</first-name>';
        }

        if ($this->getEmail() || $this->getPhone()) {
            $xml .= '<contact-data>';

            if ($this->getEmail()) {
                $xml .= '<email-addresses><email-address>';
                $xml .= '<address>' . $this->getEmail() . '</address>';
                $xml .= '<location>Work</location>';
                $xml .= '</email-address></email-addresses>';
            }

            if ($this->getPhone()) {
                $xml .= '<phone-numbers><phone-number>';
                $xml .= '<number>' . $this->getPhone() . '</number>';
                $xml .= '<location>Work</location>';
                $xml .= '</phone-number></phone-numbers>';
            }

            $xml .= '</contact-data>';
        }

        if (count($this->getCustomFields()) > 0) {
            $xml .= '<subject_datas type="array">';

            foreach ($this->getCustomFields() as $_id => $_value) {
                $xml .= '<subject_data>';
                $xml .= '<value>' . $_value . '</value>';
                $xml .= '<subject_field_id type="integer">';
                $xml .= $_id;
                $xml .= '</subject_field_id>';
                $xml .= '</subject_data>';
            }

            $xml .= '</subject_datas>';
        }

        $xml .= '</person>';
        return $xml;
    }

    /**
     * @param \SimpleXMLElement $_person
     * @return Person
     */
    public static function factory(\SimpleXMLElement $_person)
    {
        $person = new self;

        $person->setId((int) $_person->{'id'});
        $person->setLastName((string) $_person->{'last-name'});
        $person->setFirstName((string) $_person->{'first-name'});
        $person->setEmail((string) $_person->{'body'});

        $contacts = $_person->{'contact-data'};

        if ($contacts->{'email-addresses'}) {
            foreach ($contacts->{'email-addresses'}->{'email-address'} as $email) {
                $person->setEmail((string) $email->{'address'});
                break;
            }
        }

        if ($contacts->{'phone-numbers'}) {
            foreach ($contacts->{'phone-numbers'}->{'phone-number'} as $phone) {
                $person->setPhone((string) $phone->{'number'});
                break;
            }
        }

        if ($_person->{'subject_datas'}->{'subject_data'}) {
            foreach ($_person->{'subject_datas'}->{'subject_data'} as $_data) {
                $person->setCustomField(
                    (int) $_data->{'subject_field_id'},
                    (string) $_data->{'value'}
                );
            }
        }

        return $person;
    }
}

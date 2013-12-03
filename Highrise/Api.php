<?php

namespace Highrise;

class Api
{
    /** @var string */
    protected $_url;

    /** @var string */
    protected $_apiToken;

    public function initCurl($_uri)
    {
        $curl = curl_init($this->_url . $_uri);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERPWD, $this->_apiToken . ':X');

        return $curl;
    }

    /**
     * @param string $_companyName
     * @param string $_userToken
     */
    public function __construct($_companyName, $_userToken)
    {
        $this->_url = 'https://' . $_companyName . '.highrisehq.com';
        $this->_apiToken = $_userToken;
    }

    /**
     * @return Task[]
     */
    public function getTasks()
    {
        $curl = $this->initCurl('/tasks/all.xml');
        $xml = simplexml_load_string(curl_exec($curl));
        curl_close($curl);
        $result = array();

        foreach ($xml->{'task'} as $item) {
            $result[] = Task::factory($item);
        }

        return $result;
    }

    /**
     * @return Person[]
     */
    public function getPeople()
    {
        $time = time() - 60 * 60 * 24 * 7;
        $curl = $this->initCurl('/people.xml?since=' . date('Ymdhis', $time));
//        $curl = $this->initCurl('/people.xml');
        $xml = simplexml_load_string(curl_exec($curl));
        curl_close($curl);
        $result = array();

        foreach ($xml->{'person'} as $item) {
            $result[] = Person::factory($item);
        }

        return $result;
    }

    /**
     * @return Tag[]
     */
    public function getTags()
    {
        $curl = $this->initCurl('/tags.xml');
        $xml = simplexml_load_string(curl_exec($curl));
        curl_close($curl);
        $result = array();

        foreach ($xml->{'tag'} as $item) {
            $result[] = Tag::factory($item);
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getCustomFieldsResponse()
    {
        $curl = $this->initCurl('/subject_fields.xml');
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    /**
     * @param string $_label
     * @return bool|int
     */
    public function getCustomFieldIdByLabel($_label)
    {
        $xml = simplexml_load_string($this->getCustomFieldsResponse());

        foreach ($xml->{'subject-field'} as $item) {
            if ((string) $item->{'label'} == $_label) {
                return (int) $item->{'id'};
            }
        }

        return false;
    }

    /**
     * @param string $_type
     * @return string
     */
    public function getCategoriesResponseForType($_type)
    {
        $curl = $this->initCurl('/' . $_type . '_categories.xml');
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    /**
     * @param string $_type
     * @param string $_name
     * @return bool|int
     */
    public function getCategoryIdByTypeAndName($_type, $_name)
    {
        $xml = simplexml_load_string(
            $this->getCategoriesResponseForType($_type)
        );

        $name = \Ext\String::toLower($_name);

        foreach ($xml->{$_type . '-category'} as $item) {
            if (\Ext\String::toLower((string) $item->{'name'}) == $name) {
                return (int) $item->{'id'};
            }
        }

        return false;
    }

    /**
     * @param string $_uri
     * @param string $_xml
     * @return array
     */
    protected function _push($_uri, $_xml)
    {
        $curl = $this->initCurl($_uri);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $_xml);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array('Content-Type: application/xml')
        );

        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return array('status' => $status, 'response' => $response);
    }

    /**
     * @param Task $_task
     * @return array
     */
    public function pushTask(Task $_task)
    {
        return $this->_push('/tasks.xml', $_task->getPushXml(true));
    }

    /**
     * @param Note $_note
     * @return array
     */
    public function pushNote(Note $_note)
    {
        return $this->_push('/notes.xml', $_note->getPushXml());
    }

    /**
     * @param Person $_person
     * @return array
     */
    public function pushPerson(Person $_person)
    {
        return $this->_push('/people.xml', $_person->getPushXml());
    }

    /**
     * @param Tag $_tag
     * @return array
     */
    public function pushTag(Tag $_tag)
    {
        $uri = sprintf(
            '/%s/%d/tags.xml',
            $_tag->getSubjectType(),
            $_tag->getSubjectId()
        );

        return $this->_push($uri, $_tag->getPushXml());
    }

    /**
     * @param $_type
     * @param $_id
     * @return array
     */
    protected function _delete($_type, $_id)
    {
        $uri = sprintf('/%s/%d.xml', $_type, $_id);
        $curl = $this->initCurl($uri);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return array('status' => $status, 'response' => $response);
    }

    /**
     * @param Person $_person
     * @return array
     * @throws \Exception
     */
    public function deletePerson(Person $_person)
    {
        if (!$_person->getId()) {
            throw new \Exception('Unknown id');
        }

        return $this->_delete('people', $_person->getId());
    }

    /**
     * @param Task $_task
     * @return array
     * @throws \Exception
     */
    public function deleteTask(Task $_task)
    {
        if (!$_task->getId()) {
            throw new \Exception('Unknown id');
        }

        return $this->_delete('tasks', $_task->getId());
    }

    /**
     * @param Note $_note
     * @return array
     * @throws \Exception
     */
    public function deleteNote(Note $_note)
    {
        if (!$_note->getId()) {
            throw new \Exception('Unknown id');
        }

        return $this->_delete('notes', $_note->getId());
    }
}

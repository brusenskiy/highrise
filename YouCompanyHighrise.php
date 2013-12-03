<?php

class YouCompanyHighrise extends \Highrise\Api
{
    /**
     * @param \Highrise\Person $_person
     * @param string $_task
     * @param string $_text
     * @param array $_additionalTags
     * @throws Exception
     * @return array
     */
    public function registerFormRequest(\Highrise\Person $_person,
                                        $_task,
                                        $_text = null,
                                        array $_additionalTags = null)
    {
        if (!$_person || !$_person->getId()) {
            throw new \Exception('Unknown person');
        }

        $result = array();

        $task = new \Highrise\Task();
        $task->setBody($_task);
        $task->setFrame('tomorrow');
        $task->setSubjectType('Party');
        $task->setSubjectId($_person->getId());

        $categoryId = $this->getCategoryIdByTypeAndName(
            'task',
            'Call me back'
        );

        if ($categoryId) $task->setCategoryId($categoryId);

        $insert = $this->pushTask($task);
        if ($insert['status'] == 201) {
//            $task = \Highrise\Task::factory(
//                simplexml_load_string($insert['response'])
//            );

        } else {
            $result[] = 'Task wasn\'t created';
        }

        if ($_text) {
            $note = new \Highrise\Note();
            $note->setBody("Request:\r\n\r\n$_text");
            $note->setSubjectType('Party');
            $note->setSubjectId($_person->getId());

            $insert = $this->pushNote($note);
            if ($insert['status'] == 201) {
//                $note = \Highrise\Note::factory(
//                    simplexml_load_string($insert['response'])
//                );

            } else {
                $result[] = 'Note wasn\'t created';
            }
        }

        $appendTags = array(
            date('Y'),
            \Ext\String::toLower(\Ext\Date::getMonth(date('n'), 1)),
            'Site request'
        );

        if ($_additionalTags) {
            $appendTags = array_merge($appendTags, $_additionalTags);
        }

        foreach ($appendTags as $appendTag) {
            $tag = new \Highrise\Tag();
            $tag->setName($appendTag);
            $tag->setSubjectType('people');
            $tag->setSubjectId($_person->getId());

            $insert = $this->pushTag($tag);
            if ($insert['status'] == 201) {
//                $tag = \Highrise\Tag::factory(
//                    simplexml_load_string($insert['response'])
//                );

            } else {
                $result[] = "Tag $appendTag wasn't created";
            }
        }

        return $result;
    }

    /**
     * @param string $_name
     * @param string $_email
     * @param string $_phone
     * @param string $_referrer
     * @return bool|\Highrise\Person
     */
    public function getPerson($_name,
                              $_email = null,
                              $_phone = null,
                              $_referrer = null)
    {
        $new = new \Highrise\Person;

        $name = explode(' ', trim($_name));
        $new->setFirstName($name[0]);
        unset($name[0]);
        if (count($name) > 0) {
            $new->setLastName(implode(' ', $name));
        }

        if ($_email && \Ext\String::isEmail($_email)) $new->setEmail($_email);
        if ($_phone) $new->setPhone($_phone);

        if ($_referrer) {
            $customFieldLabel = 'Referred By';
            $referredById = $this->getCustomFieldIdByLabel($customFieldLabel);
            if ($referredById) {
                $new->setCustomField($referredById, $_referrer);
            }
        }

        $matchEmail = \Ext\String::toLower($new->getEmail());
        foreach ($this->getPeople() as $_person) {
            if (\Ext\String::toLower($_person->getEmail()) == $matchEmail) {
                return $_person;
            }
        }

        $insert = $this->pushPerson($new);
        if ($insert['status'] == 201) {
            return \Highrise\Person::factory(
                simplexml_load_string($insert['response'])
            );
        }

        return false;
    }
}

<?php
namespace Api\Operators;

use Api\Models\PersonModel;

class PersonOperator
{

    public function __construct()
    {
        $this->_personModel = new PersonModel;
    }

    /**
     * Create New Person Method
     *
     * @param array $form
     * @return array
     */
    public function addNewPerson(array $form): array
    {

        // if you sent a formData for image,
        // then remove tha array type from $form.

        $result = false;
        $msg = '';

        $data = [
            'name' => $form['title'],
            'email' => $form['email'],
            'gender' => (int) ($form['gender']),
        ];

        if ($this->_personModel->addPerson($data)) {
            $result = true;
            $msg = 'Person successfully saved!';
        } else {
            $result = true;
            $msg = 'Something went wrong, Please try again';
        }

        return [
            'success' => $result,
            'msg' => $msg,
            'id' => $this->_personModel->getLastId(),
        ];
    }

    /**
     * Edit Person Info From List
     *
     * @param array $form
     * @param integer $id
     * @return array|null
     */
    public function editPerson(array $form, int $id): ?array
    {
        $data = [
            'name' => $form['name'],
            'email' => $form['email'],
            'gender' => (int) ($form['gender']),
        ];
        // check for any changes

        $result = false;
        $msg = '';

        if ($this->_personModel->updatePerson($data, $id)) {
            $result = true;
            $msg = 'Person successfully updated!';
        } else {
            $result = true;
            $msg = 'Something went wrong, please try again';
        }

        return [
            'success' => $result,
            'msg' => $msg,
        ];
    }

    /**
     * Delete Person Method
     *
     * @param integer $id
     * @return boolean
     */
    public function deletePerson(int $id): bool
    {
        return $this->_personModel->deletePerson($id);
    }

}

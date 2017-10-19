<?php

class WebUser extends CWebUser {

    private $_roles = null;

    /**
     * Получение ролей пользователя
     *
     * @return array
     */
    function getRole() {
		return $this->getRolesFromDB();
    }
	
	
	/**
     * Enter description here...
     *
     * @return unknown
     */
    private function getRolesFromDB() {

        if (!$this->isGuest && ($this->_roles === null)) {

            $connection = Yii::app()->db;
			$table = Users::model()->tableName();

            $sql = "SELECT `role` FROM `{$table}` WHERE `id`= :id";

            $command = $connection->createCommand($sql);
            $command->bindValue(':id', $this->id);

            $this->_roles = $command->queryScalar();
            $command->getPdoStatement()->closeCursor();
        }

        return $this->_roles;
    }
}
<?php
class Zephyrtheme_Model_DbTable_SliderItems extends Engine_Db_Table
{
    protected $_serializedColumns = array('show_item');

    public function getColumns()
    {
        return $this->_getCols();
    }

    public function getLastInsertId()
    {
        return $this->_db->lastInsertId();
    }
}
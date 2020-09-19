<?php
require_once MODX_CORE_PATH . 'model/modx/processors/resource/getnodes.class.php';

class CourseGetNodesProcessor extends modResourceGetNodesProcessor {
    public $contextKey = 'web';

    public function getRootNode() {
        $this->defaultRootId = 3;

        $id = $this->getProperty('id');
        if (empty($id) || $id == 'root') {
            $this->startNode = $this->defaultRootId;
        } else {
            $parts= explode('_',$id);
            $this->contextKey= isset($parts[0]) ? $parts[0] : false;
            $this->startNode = !empty($parts[1]) ? intval($parts[1]) : 0;
        }
        if ($this->getProperty('debug')) {
            echo '<p style="width: 800px; font-family: \'Lucida Console\'; font-size: 11px">';
        }
    }
}

return 'CourseGetNodesProcessor';
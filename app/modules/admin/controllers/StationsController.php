<?php
use \Entity\Station;
use \Entity\Station as Record;

class Admin_StationsController extends \DF\Controller\Action
{
    public function permissions()
    {
        return \DF\Acl::getInstance()->isAllowed('administer stations');
    }
    
    public function indexAction()
    {
        $records = $this->em->createQuery('SELECT s FROM Entity\Station s ORDER BY s.category ASC, s.weight ASC, s.id ASC')
            ->getArrayResult();

        $stations_by_category = array();
        $pending_stations = array();

        foreach($records as $station)
        {
            if ($station['is_active'] || $station['is_special'])
                $stations_by_category[$station['category']][] = $station;
            else
                $pending_stations[] = $station;
        }

        $this->view->categories = $stations_by_category;
        $this->view->pending_stations = $pending_stations;
    }
    
    public function editAction()
    {
        $form = new \DF\Form($this->current_module_config->forms->station);
        
        if ($this->_hasParam('id'))
        {
            $id = (int)$this->_getParam('id');
            $record = Record::find($id);
            $form->setDefaults($record->toArray(FALSE, TRUE));
        }

        if($_POST && $form->isValid($_POST) )
        {
            $data = $form->getValues();

            if (!($record instanceof Record))
                $record = new Record;

            $files = $form->processFiles('stations');

            foreach($files as $file_field => $file_paths)
                $data[$file_field] = $file_paths[1];

            \DF\Image::resizeImage($data['image_url'], $data['image_url'], 150, 150);
            
            $record->fromArray($data);
            $record->save();

            // Clear station cache.
            \DF\Cache::remove('stations');

            $this->alert('Changes saved.', 'green');
            $this->redirectFromHere(array('action' => 'index', 'id' => NULL));
            return;
        }

        $this->view->headTitle('Edit Record');
        $this->renderForm($form);
    }
    
    public function deleteAction()
    {
        $record = Record::find($this->_getParam('id'));
        if ($record)
            $record->delete();
            
        $this->alert('Record deleted.', 'green');
        $this->redirectFromHere(array('action' => 'index', 'id' => NULL, 'csrf' => NULL));
    }
}
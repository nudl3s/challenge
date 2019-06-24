<?php
namespace App\Controller;

use Cake\Chronos\Date;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use App\Model\Table\FilesTable;

class PagesController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->loadModel('Files');
    }

    public function home()
    {
        $videoFiles = TableRegistry::getTableLocator()->get('Files')->find();

        $this->set('videoFiles', $videoFiles);
    }

    /**
     *
     * @throws \Exception
     */
    public function upload()
    {
        $file = '';
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->layout = 'ajax';

            if (!empty($this->request->getData('file'))) {
                $filename = $this->request->getData('file')['name'];
                $size = $this->request->getData('file')['size'];
                $type = $this->request->getData('file')['type'];
                $url = Router::url('/', true) . 'files/' . $filename;
                $uploadPath = 'files/';
                $uploadFile = $uploadPath . $filename;
                if (move_uploaded_file($this->request->getData('file')['tmp_name'], $uploadFile)) {
                    $files = TableRegistry::getTableLocator()->get('Files');
                    $file = $files->newEntity([
                        'name' => $filename,
                        'path' => $url,
                        'size' => $size,
                        'extension' => $type,
                        'created' => new \DateTime('now')
                    ]);
                    if ($files->save($file)) {
                        $urlVideo = $url = Router::url(['controller' => 'Pages', 'action' => 'single', $file->id,'_full' => true]);
                        echo json_encode(array('id' => $file->id, 'name' => $filename, 'url' => $urlVideo));
                    }
                }
            }
        }

        $this->set('file', $file);
    }

    public function single($id) {
        $videoFiles = TableRegistry::getTableLocator()->get('Files');
        $video = $videoFiles->get($id);

        $this->set('video', $video);
    }
}

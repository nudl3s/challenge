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

                    } else {

                    }
                } else {

                }
            }
        }
//
//        if ($this->request->is('post')) {
//            if (!empty($this->request->getData('file'))) {
//                $filename = $this->request->getData('file')['name'];
//                $size = $this->request->getData('file')['size'];
//                $tmp = explode('.', $filename);
//                $file_extension = end($tmp);
//                $url = Router::url('/', true) . 'files/' . $filename;
//                $uploadPath = 'files/';
//                $uploadFile = $uploadPath . $filename;
//                if (move_uploaded_file($this->request->getData('file')['tmp_name'], $uploadFile)) {
//                    $articles = TableRegistry::getTableLocator()->get('Files');
//                    $article = $articles->newEntity([
//                        'name' => $filename,
//                        'path' => $url,
//                        'size' => $size,
//                        'extension' => $file_extension,
//                        'created' => new \DateTime('now')
//                    ]);
//                    if ($articles->save($article)) {
//                        $this->Flash->success(__('File uploaded successfully.'));
//                    } else {
//                        $this->Flash->error(__('Fail uploading file.'));
//                    }
//                } else {
//                    $this->Flash->error(__('Fail uploading file.'));
//                }
//            } else {
//                $this->Flash->error(__('First choose a file to upload.'));
//            }
//        }
        $this->set('file', $file);
    }

    public function single($id) {
        $videoFiles = TableRegistry::getTableLocator()->get('Files');
        $video = $videoFiles->get($id);

        $this->set('video', $video);
    }
}

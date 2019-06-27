<?php
namespace App\Controller;

use Cake\Chronos\Date;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use App\Model\Table\FilesTable;
use GuzzleHttp\Exception\ConnectException;
use TusPhp\Exception\ConnectionException;
use TusPhp\Exception\FileException;
use TusPhp\Exception\TusException;

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

        $server = Router::url(array('controller' => 'Tus', 'action' => 'server'), true);
        $client = new \TusPhp\Tus\Client($server, ['verify' => false]);

        $client->setApiPath('/challenge/tus');


        $file = '';
        if ($this->request->is('ajax')) {
            $this->autoRender = false;
            $this->layout = 'ajax';

            if (!empty($this->request->getData('file'))) {
                $fileMeta  = $this->request->getData('file');
                $uploadKey = hash_file('md5', $fileMeta['tmp_name']);
                try {
                    $client->setKey($uploadKey)->file($fileMeta['tmp_name'], time() . '_' . $fileMeta['name']);
                    $bytesUploaded = $client->upload(5000000); // Chunk of 5 mb
                    echo json_encode([
                        'status' => 'uploading',
                        'bytes_uploaded' => $bytesUploaded,
                        'upload_key' => $uploadKey
                    ]);
                }
                catch( ConnectionException $e )
                {
                    echo json_encode([
                        'status' => 'error',
                        'bytes_uploaded' => -1,
                        'upload_key' => '',
                        'error' => $e->getMessage(),
                    ]);
                }
                catch ( FileException $b )
                {
                    echo json_encode([
                        'status' => 'error',
                        'bytes_uploaded' => -1,
                        'upload_key' => '',
                        'error' => $b->getMessage(),
                    ]);
                }
                catch ( TusException $c )
                {
                    echo json_encode([
                        'status' => 'error',
                        'bytes_uploaded' => -1,
                        'upload_key' => '',
                        'error' => $c->getMessage(),
                    ]);
                }
            }
        }

        $this->set('file', $file);
    }

    /**
     * @throws \ReflectionException
     */
    public function verify() {
        $this->autoRender = false;
        $this->layout = 'ajax';
        $server = Router::url(array('controller' => 'Tus', 'action' => 'server'), true);
        $client = new \TusPhp\Tus\Client($server, ['verify' => false]);
        $client->setApiPath('/challenge/tus');

        $uploadKey = uniqid('file');
        try {
            $offset = $client->setKey($uploadKey)->getOffset();
            $status = false !== $offset ? 'resume' : 'new';
            $offset = false === $offset ? 0 : $offset;
            echo json_encode([
                'status' => $status,
                'bytes_uploaded' => $offset,
                'upload_key' => $uploadKey,
            ]);
        } catch (ConnectException $e) {
            echo json_encode([
                'status' => 'error',
                'bytes_uploaded' => -1,
            ]);
        } catch (FileException $e) {
            echo json_encode([
                'status' => 'resume',
                'bytes_uploaded' => 0,
                'upload_key' => '',
            ]);
        }
    }

    public function single($id) {
        $videoFiles = TableRegistry::getTableLocator()->get('Files');
        $video = $videoFiles->get($id);

        $this->set('video', $video);
    }
}
